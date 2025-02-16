<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Mail\AssignmentCreated;
use Illuminate\Support\Facades\Mail;

/**
 * @OA\Schema(
 *     schema="AssignmentResource",
 *     type="object",
 *     required={"course_id", "title", "description", "deadline"},
 *     @OA\Property(property="course_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Assignment Title"),
 *     @OA\Property(property="description", type="string", example="Assignment Description"),
 *     @OA\Property(property="deadline", type="string", format="date-time", example="2023-12-31 23:59:59")
 * )
 */
class AssignmentController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/assignments",
     *     summary="Create a new assignment",
     *     tags={"Assignments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"course_id","title","description","deadline"},
     *             @OA\Property(property="course_id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Assignment Title"),
     *             @OA\Property(property="description", type="string", example="Assignment Description"),
     *             @OA\Property(property="deadline", type="string", format="date-time", example="2023-12-31 23:59:59")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assignment created successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AssignmentResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation Error."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'course_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date_format:Y-m-d H:i:s'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input['deadline'] = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $input['deadline']);
        $Assignment = Assignment::create($input);

        // Send email to users with role 'student'
        $users = User::where('role', 'student')->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new AssignmentCreated($Assignment));
        }

        return $this->sendResponse(new AssignmentResource($Assignment), 'Assignment created successfully.');
    }
}
