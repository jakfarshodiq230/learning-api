<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\MaterialResource;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $Material = Material::all();

        return $this->sendResponse(MaterialResource::collection($Material), 'Material retrieved successfully.');
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
            'title' => 'required|string',
            'file_path' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $Material = Material::create($input);

        return $this->sendResponse(new MaterialResource($Material), 'Material created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        $Material = Material::find($id);

        if (is_null($Material)) {
            return $this->sendError('Material not found.');
        }

        return $this->sendResponse(new MaterialResource($Material), 'Material retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Material $Material): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'course_id' => 'required|integer',
            'title' => 'required|string',
            'file_path' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $Material->course_id = $input['course_id'];
        $Material->title = $input['title'];
        $Material->file_path = $input['file_path'];
        $Material->save();

        return $this->sendResponse(new MaterialResource($Material), 'Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Material $Material): JsonResponse
    {
        $Material->delete();

        return $this->sendResponse([], 'Material deleted successfully.');
    }
}
