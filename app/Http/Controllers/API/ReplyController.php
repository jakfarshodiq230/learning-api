<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\ReplyResource;
use App\Models\Discussion;
use App\Models\Reply;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ReplyController extends BaseController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $discussion_id): JsonResponse
    {
        $input = $request->all();
        if (!Discussion::find($discussion_id)) {
            return $this->sendError('Discussion not found.');
        }
        $input['discussion_id'] = $discussion_id;

        $validator = Validator::make($input, [
            'discussion_id' => 'required|integer',
            'user_id' => 'required|integer',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $reply = Reply::create($input);

        return $this->sendResponse(new ReplyResource($reply), 'Reply created successfully.');
    }
}
