<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\ExamResultExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Result;
use App\Models\Exam;
use App\Models\Examinee;
use App\Models\ExamResponse;
use App\Models\Question;
use App\Models\Unit;

class ResultController extends Controller
{
    public function index()
    {
        $exams = Exam::all();
        $examinees = Examinee::with('responses')->get()->map(function ($examinee) {
            $examinee->full_name = $examinee->rank . ' ' . $examinee->first_name . ' ' . 
                                 $examinee->middle_name . ' ' . $examinee->last_name . ' ' . 
                                 $examinee->qualifier;
            return $examinee;
        });

        // Get unique designations and units for filters
        $designations = Examinee::select('designation')->distinct()->get()->pluck('designation');
        $units = Unit::whereIn('UnitId', Examinee::distinct()->pluck('unit'))
                ->pluck('OrderNumberPrefix', 'UnitId');

        return view('admin.resultss', compact('exams', 'examinees', 'designations', 'units'));
    }

    public function postResultsData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'examFilter' => 'nullable|string',
                'nameFilter' => 'nullable|string',
                'designationFilter' => 'nullable|string',
                'unitFilter' => 'nullable|string',
                'start' => 'integer|min:0',
                'length' => 'integer|min:1|max:1000',
                'draw' => 'required|integer',
                'order' => 'nullable|array',
                'order.0.column' => 'nullable|integer',
                'order.0.dir' => 'nullable|in:asc,desc'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $validated = $validator->validated();

            // Base query to get examinees with their exam results
            $query = Examinee::with(['responses.question.exam', 'token', 'unit'])
                ->select('examinees.*')
                ->addSelect([
                    'total_questions' => ExamResponse::selectRaw('COUNT(DISTINCT question_id)')
                        ->whereColumn('examinee_id', 'examinees.id'),
                    'score' => ExamResponse::selectRaw('SUM(is_correct)')
                        ->whereColumn('examinee_id', 'examinees.id')
                ]);

            // Apply filters
            if (($validated['examFilter'] ?? 'all') != 'all') {
                $query->whereHas('responses.question', function($q) use ($validated) {
                    $q->where('exam_id', $validated['examFilter']);
                });
            }

            if (($validated['nameFilter'] ?? 'all') != 'all') {
                $query->where('id', $validated['nameFilter']);
            }

            if (($validated['designationFilter'] ?? 'all') != 'all') {
                $query->where('designation', $validated['designationFilter']);
            }

            if (($validated['unitFilter'] ?? 'all') != 'all') {
                $query->where('unit', $validated['unitFilter']);
            }

            // Get total count before pagination
            $totalData = $query->count();

            // Pagination
            $start = $validated['start'] ?? 0;
            $length = $validated['length'] ?? 20;
            
            if ($length == -1) {
                $examinees = $query->skip($start)->get();
            } else {
                $examinees = $query->skip($start)->take($length)->get();
            }

            // Format data for DataTables
            $data = $examinees->map(function ($examinee, $index) use ($start) {
                $totalQuestions = $examinee->total_questions ?? 0;
                $score = $examinee->score ?? 0;
                $percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100) : 0;

                // Determine rating based on percentage
                if ($percentage >= 95) {
                    $rating = 'Passed (100%)';
                } elseif ($percentage >= 90) {
                    $rating = 'Passed (94%)';
                } elseif ($percentage >= 85) {
                    $rating = 'Passed (89%)';
                } elseif ($percentage >= 80) {
                    $rating = 'Passed (84%)';
                } elseif ($percentage >= 76) {
                    $rating = 'Passed (80%)';
                } elseif ($percentage == 75) {
                    $rating = 'Passed (75%)';
                } else {
                    $rating = 'Failed (' . $percentage . '%)';
                }

                // Get exam title (assuming each examinee took one exam)
                $examTitle = $examinee->responses->first()->question->exam->title ?? 'N/A';

                // Get the Description of unit
                $unitDescription = $examinee->unit_description;

                return [
                    'id' => $examinee->id,
                    'number' => $start + $index + 1,
                    'exam' => $examTitle,
                    'name' => $examinee->full_name,
                    'designation' => $examinee->designation,
                    'unit' => $unitDescription,
                    'total_question' => $totalQuestions,
                    'score' => $score,
                    'percentage' => $percentage . '%',
                    'rating' => $rating,
                    'action' => $this->getActionButtons($examinee)
                ];
            });

            return response()->json([
                'draw' => intval($validated['draw']),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalData,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in postResultsData: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while processing the request'
            ], 500);
        }
    }

    private function getActionButtons($result)
    {
        return '<div class="btn-group btn-group-sm">
            <a href="' . route('results.export', ['id' => $result->id, 'type' => 'excel']) . '" 
                class="btn btn-outline-success">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="' . route('results.export', ['id' => $result->id, 'type' => 'pdf']) . '" 
                class="btn btn-outline-danger">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </div>';
    }

    public function exportResult($id, $type = 'excel')
    {
        try {
            $result = ExamResult::with(['examinee', 'exam', 'examinee.responses.question'])
                        ->findOrFail($id);

            $filename = 'Exam_Result_' . str_replace(' ', '_', $result->examinee->full_name) . '_' . now()->format('Ymd_His');

            if ($type === 'pdf') {
                $pdf = Pdf::loadView('admin.exports.exam-result-pdf', ['result' => $result]);
                return $pdf->download($filename . '.pdf');
            }

            // Default to Excel export
            return Excel::download(
                new ExamResultExport($result), 
                $filename . '.xlsx'
            );

        } catch (\Exception $e) {
            \Log::error("Export failed for result ID {$id}: " . $e->getMessage());
            return back()->with('error', 'Failed to generate export. Please try again.');
        }
    }
}