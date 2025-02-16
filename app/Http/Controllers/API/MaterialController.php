<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\MaterialResource;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @OA\Schema(
 *     schema="MaterialResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="course_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Material Title"),
 *     @OA\Property(property="file_path", type="string", example="material/file.pdf"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 */
class MaterialController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/materials",
     *     summary="Store a newly created material",
     *     tags={"Materials"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"course_id", "title", "file"},
     *                 @OA\Property(property="course_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Material Title"),
     *                 @OA\Property(property="file", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Material created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/MaterialResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation Error."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
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

        $material = Material::create($input);

        return $this->sendResponse(new MaterialResource($material), 'Material created successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/materials/{id}/download",
     *     summary="Download a material file",
     *     tags={"Materials"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File downloaded successfully",
     *         @OA\MediaType(
     *             mediaType="application/octet-stream",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Material or File not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Material not found or File not found.")
     *         )
     *     )
     * )
     */
    public function download($id): BinaryFileResponse|JsonResponse
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
