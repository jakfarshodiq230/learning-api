<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\CourseResource;
use Illuminate\Support\Facades\Validator;

class CourseController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $Course = Course::all();

        return $this->sendResponse(CourseResource::collection($Course), 'Course retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Course $Course): JsonResponse
    {
        $Course->delete();

        return $this->sendResponse([], 'Course deleted successfully.');
    }

    /**
     * Enroll a user in the specified course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
