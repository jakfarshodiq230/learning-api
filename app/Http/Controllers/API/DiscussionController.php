<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\DiscussionResource;
use App\Models\Discussion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscussionController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $Discussion = Discussion::all();

        return $this->sendResponse(DiscussionResource::collection($Discussion), 'Discussion retrieved successfully.');
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
            'user_id' => 'required|integer',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $Discussion = Discussion::create($input);

        return $this->sendResponse(new DiscussionResource($Discussion), 'Discussion created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        $Discussion = Discussion::find($id);

        if (is_null($Discussion)) {
            return $this->sendError('Discussion not found.');
        }

        return $this->sendResponse(new DiscussionResource($Discussion), 'Discussion retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Discussion $Discussion): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'course_id' => 'required|integer',
            'user_id' => 'required|integer',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $Discussion->course_id = $input['course_id'];
        $Discussion->user_id = $input['user_id'];
        $Discussion->content = $input['content'];
        $Discussion->save();

        return $this->sendResponse(new DiscussionResource($Discussion), 'Discussion updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Discussion $Discussion): JsonResponse
    {
        $Discussion->delete();

        return $this->sendResponse([], 'Discussion deleted successfully.');
    }
}
