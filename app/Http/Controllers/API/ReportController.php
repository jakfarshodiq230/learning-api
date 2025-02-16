<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(
 *     schema="Course",
 *     type="object",
 *     title="Course",
 *     properties={
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             description="Course ID"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             description="Course name"
 *         ),
 *         @OA\Property(
 *             property="students_count",
 *             type="integer",
 *             description="Number of students enrolled"
 *         ),
 *         @OA\Property(
 *             property="assignments",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Assignment")
 *         )
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="Assignment",
 *     type="object",
 *     title="Assignment",
 *     properties={
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             description="Assignment ID"
 *         ),
 *         @OA\Property(
 *             property="title",
 *             type="string",
 *             description="Assignment title"
 *         ),
 *         @OA\Property(
 *             property="graded_submissions_count",
 *             type="integer",
 *             description="Number of graded submissions"
 *         ),
 *         @OA\Property(
 *             property="ungraded_submissions_count",
 *             type="integer",
 *             description="Number of ungraded submissions"
 *         ),
 *         @OA\Property(
 *             property="submissions",
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Submission")
 *         )
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="Submission",
 *     type="object",
 *     title="Submission",
 *     properties={
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             description="Submission ID"
 *         ),
 *         @OA\Property(
 *             property="student_id",
 *             type="integer",
 *             description="Student ID"
 *         ),
 *         @OA\Property(
 *             property="graded_at",
 *             type="string",
 *             format="date-time",
 *             description="Date and time when the submission was graded"
 *         )
 *     }
 * )
 */
class ReportController extends BaseController
{

    /**
     * @OA\Get(
     *     path="/api/reports/courses",
     *     summary="Get course statistics",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Course")
     *         )
     *     )
     * )
     */
    public function courseStatistics(): JsonResponse
    {
        $courseStatistics = Course::withCount('students')->get();

        return response()->json($courseStatistics);
    }

    /**
     * @OA\Get(
     *     path="/api/reports/assignments",
     *     summary="Get assignment statistics",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Course")
     *         )
     *     )
     * )
     */
    public function assignmentStatistics(): JsonResponse
    {
        $assignmentStatistics = Course::with(['assignments' => function ($query) {
            $query->withCount(['submissions as graded_submissions_count' => function ($query) {
                $query->whereNotNull('graded_at');
            }, 'submissions as ungraded_submissions_count' => function ($query) {
                $query->whereNull('graded_at');
            }]);
        }])->get();

        return response()->json($assignmentStatistics);
    }

    /**
     * @OA\Get(
     *     path="/api/reports/students/{id}",
     *     summary="Get student assignment and grade statistics",
     *     tags={"Reports"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Course")
     *         )
     *     )
     * )
     */
    public function studentAssignmentAndGradeStatistics(int $studentId): JsonResponse
    {
        $studentStatistics = Course::with(['assignments' => function ($query) use ($studentId) {
            $query->with(['submissions' => function ($query) use ($studentId) {
                $query->where('student_id', $studentId)
                    ->withCount(['submissions as graded_submissions_count' => function ($query) {
                        $query->whereNotNull('graded_at');
                    }, 'submissions as ungraded_submissions_count' => function ($query) {
                        $query->whereNull('graded_at');
                    }]);
            }]);
        }])->get();

        return response()->json($studentStatistics);
    }
}
