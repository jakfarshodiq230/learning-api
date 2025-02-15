<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;

class SubmissionController extends BaseController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($student_id, Request $request): JsonResponse
    {
        $input = $request->all();

        // Check if the student_id exists in the users table
        if (User::find($student_id)) {
            return $this->sendError('Student not found.');
        }

        $validator = Validator::make($input, [
            'assignment_id' => 'required|integer',
            'file_path' => 'required|string',
            'score' => 'required|integer|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input['student_id'] = $student_id;
        $Submission = Submission::create($input);

        return $this->sendResponse(new SubmissionResource($Submission), 'Submission created successfully.');
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
            'file_path' => 'required|file|mimes:pdf,doc,docx,zip|max:2048',
            'score' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($request->file('file_path')->isValid()) {
            $path = $request->file('file_path')->store('submissions');

            return $this->sendResponse(['file_path' => $path], 'File uploaded successfully.');
        }

        return $this->sendError('File upload failed.');
    }
}
