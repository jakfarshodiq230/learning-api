<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class ReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function courseStatistics(): JsonResponse
    {
        $courseStatistics = Course::withCount('students')->get();

        return response()->json($courseStatistics);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Display the assignment and grade statistics for a specific student.
     *
     * @param int $studentId
     * @return \Illuminate\Http\JsonResponse
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
