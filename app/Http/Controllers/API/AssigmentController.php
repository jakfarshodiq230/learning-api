<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Assignment;
use Validator;
use App\Http\Resources\AssigmentResource;
use Illuminate\Http\JsonResponse;

class AssigmentController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $Assignment = Assignment::all();

        return $this->sendResponse(AssigmentResource::collection($Assignment), 'Assignment retrieved successfully.');
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
            'course_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date_format:Y-m-d H:i:s'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $Assignment = Assignment::create($input);

        return $this->sendResponse(new AssigmentResource($Assignment), 'Assignment created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        $Assignment = Assignment::find($id);

        if (is_null($Assignment)) {
            return $this->sendError('Assignment not found.');
        }

        return $this->sendResponse(new AssigmentResource($Assignment), 'Assignment retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Assignment $Assignment): JsonResponse
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

        $Assignment->course_id = $input['course_id'];
        $Assignment->title = $input['title'];
        $Assignment->description = $input['description'];
        $Assignment->deadline = $input['deadline'];
        $Assignment->save();

        return $this->sendResponse(new AssigmentResource($Assignment), 'Assignment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Assignment $Assignment): JsonResponse
    {
        $Assignment->delete();

        return $this->sendResponse([], 'Assignment deleted successfully.');
    }
}
