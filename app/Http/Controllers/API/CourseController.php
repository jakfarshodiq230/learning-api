<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\CourseResource;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Schema(
 *     schema="CourseResource",
 *     type="object",
 *     title="Course Resource",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the course"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the course"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the course"
 *     ),
 *     @OA\Property(
 *         property="lecturer_id",
 *         type="integer",
 *         description="ID of the lecturer"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp"
 *     )
 * )
 */
class CourseController extends BaseController
{


    /**
     * @OA\Get(
     *     path="/api/courses",
     *     summary="Get list of courses",
     *     tags={"Courses"},
     *     @OA\Response(
     *         response=200,
     *         description="Courses retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CourseResource")
     *         )
     *     )
     * )
     */

    public function index(): JsonResponse
    {
        $Course = Course::all();

        return $this->sendResponse(CourseResource::collection($Course), 'Course retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/courses",
     *     summary="Create a new course",
     *     tags={"Courses"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "lecturer_id"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="lecturer_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Course created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CourseResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'lecturer_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $Course = Course::create($input);

        return $this->sendResponse(new CourseResource($Course), 'Course created successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/courses/{id}",
     *     summary="Get a specific course",
     *     tags={"Courses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CourseResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $Course = Course::find($id);

        if (is_null($Course)) {
            return $this->sendError('Course not found.');
        }

        return $this->sendResponse(new CourseResource($Course), 'Course retrieved successfully.');
    }
    /**
     * @OA\Put(
     *     path="/api/courses/{id}",
     *     summary="Update a specific course",
     *     tags={"Courses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "lecturer_id"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="lecturer_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CourseResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

    public function update(Request $request, Course $Course): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'lecturer_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $Course->name = $input['name'];
        $Course->description = $input['description'];
        $Course->lecturer_id = $input['lecturer_id'];
        $Course->save();

        return $this->sendResponse(new CourseResource($Course), 'Course updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/courses/{id}",
     *     summary="Delete a specific course",
     *     tags={"Courses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(Course $Course): JsonResponse
    {
        $Course->delete();

        return $this->sendResponse([], 'Course deleted successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/courses/{id}/enroll",
     *     summary="Enroll a user in a course",
     *     tags={"Courses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User enrolled in course successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CourseResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function enroll(Request $request, $id): JsonResponse
    {
        $Course = Course::find($id);

        if (is_null($Course)) {
            return $this->sendError('Course not found.');
        }

        $user = $request->user();
        $Course->students()->attach($user->id);

        return $this->sendResponse(new CourseResource($Course), 'User enrolled in course successfully.');
    }
}
