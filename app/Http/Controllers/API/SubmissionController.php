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

/**
 * @OA\Schema(
 *     schema="SubmissionResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="assignment_id", type="integer"),
 *     @OA\Property(property="student_id", type="integer"),
 *     @OA\Property(property="file_path", type="string"),
 *     @OA\Property(property="score", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class SubmissionController extends BaseController
{

    /**
     * @OA\Put(
     *     path="/api/submissions/{id}/grade",
     *     summary="Update the grade of a submission",
     *     tags={"Submissions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="grade", type="integer", example=85)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Grade updated and email sent successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SubmissionResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation Error.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Submission not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Submission not found.")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/submissions",
     *     summary="Upload a new submission",
     *     tags={"Submissions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="assignment_id", type="integer"),
     *                 @OA\Property(property="student_id", type="integer"),
     *                 @OA\Property(property="file_path", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File uploaded and email sent successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SubmissionResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation Error.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="File upload failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="File upload failed.")
     *         )
     *     )
     * )
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
