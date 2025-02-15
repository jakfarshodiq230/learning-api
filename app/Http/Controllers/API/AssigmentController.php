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
}
