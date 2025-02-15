<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Mail\SubmissionCreated;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SubmissionController extends BaseController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateScore($id, Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'score' => 'required|integer|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $submission = Submission::find($id);

        if (!$submission) {
            return $this->sendError('Submission not found.');
        }

        $submission->score = $request->score;
        $submission->save();

        // Send email to the student with the updated score
        $student = User::find($submission->student_id);
        $assignment = Assignment::find($submission->assignment_id);
        Mail::to($student->email)->send(new SubmissionCreated($assignment, $request->score));

        return $this->sendResponse(new SubmissionResource($submission), 'Score updated and email sent successfully.');
    }


    /**
     * Upload a file for submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|integer',
            'student_id' => 'required|integer',
            //'file_path' => 'required|file|mimes:pdf,doc,docx,zip|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($request->file('file_path')->isValid()) {
            $path = $request->file('file_path')->store('submissions');

            $input = $request->all();
            $input['file_path'] = $path;
            $submission = Submission::create($input);

            return $this->sendResponse(new SubmissionResource($submission), 'File uploaded and email sent successfully.');
        }

        return $this->sendError('File upload failed.');
    }
}
