<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ReplyController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $Reply = Reply::all();

        return $this->sendResponse(ReplyResource::collection($Reply), 'Reply retrieved successfully.');
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
            'discussion_id' => 'required|integer',
            'user_id' => 'required|integer',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $Reply = Reply::create($input);

        return $this->sendResponse(new ReplyResource($Reply), 'Reply created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        $Reply = Reply::find($id);

        if (is_null($Reply)) {
            return $this->sendError('Reply not found.');
        }

        return $this->sendResponse(new ReplyResource($Reply), 'Reply retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Reply $Reply): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'discussion_id' => 'required|integer',
            'user_id' => 'required|integer',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $Reply->discussion_id = $input['discussion_id'];
        $Reply->user_id = $input['user_id'];
        $Reply->content = $input['content'];
        $Reply->save();

        return $this->sendResponse(new ReplyResource($Reply), 'Reply updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Reply $Reply): JsonResponse
    {
        $Reply->delete();

        return $this->sendResponse([], 'Reply deleted successfully.');
    }
}
