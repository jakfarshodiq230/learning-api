<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\AssigmentResource;
use App\Models\Assignment;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\AssignmentResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Mail\AssignmentCreated;
use Illuminate\Support\Facades\Mail;

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

        $input['deadline'] = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $input['deadline']);
        $Assignment = Assignment::create($input);

        // Send email to users with role 'mahasiswa'
        $users = User::where('role', 'mahasiswa')->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new AssignmentCreated($Assignment));
        }

        return $this->sendResponse(new AssigmentResource($Assignment), 'Assignment created successfully.');
    }
}
