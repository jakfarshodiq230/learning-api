<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;

class SubmissionController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $Submission = Submission::all();

        return $this->sendResponse(SubmissionResource::collection($Submission), 'Submission retrieved successfully.');
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

        $Submission = Submission::create($input);

        return $this->sendResponse(new SubmissionResource($Submission), 'Submission created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        $Submission = Submission::find($id);

        if (is_null($Submission)) {
            return $this->sendError('Submission not found.');
        }

        return $this->sendResponse(new SubmissionResource($Submission), 'Submission retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Submission $Submission): JsonResponse
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

        $Submission->discussion_id = $input['discussion_id'];
        $Submission->user_id = $input['user_id'];
        $Submission->content = $input['content'];
        $Submission->save();

        return $this->sendResponse(new SubmissionResource($Submission), 'Submission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Submission $Submission): JsonResponse
    {
        $Submission->delete();

        return $this->sendResponse([], 'Submission deleted successfully.');
    }
}
