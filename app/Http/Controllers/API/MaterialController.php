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
            'file' => 'required|file|mimes:pdf,doc,docx,zip'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('material', 'public');
            $input['file_path'] = $filePath;
        }

        $Material = Material::create($input);

        return $this->sendResponse(new MaterialResource($Material), 'Material created successfully.');
    }

    /**
     * Download the specified material file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id)
    {
        $material = Material::find($id);

        if (is_null($material)) {
            return $this->sendError('Material not found.');
        }

        $filePath = storage_path('app/public/' . $material->file_path);

        if (!file_exists($filePath)) {
            return $this->sendError('File not found.');
        }

        return response()->download($filePath);
    }
}
