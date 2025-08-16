<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Question;
use App\Models\Exam;
use App\Models\ExamResponse;

class AnalysisController extends Controller
{
    public function index()
    {
        $exams = Exam::all();
        $types = Question::getEnumValues('type');
        $difficulties = Question::getEnumValues('difficulty');

        return view('admin.analysis.question', compact('exams', 'types', 'difficulties'));
    }

    public function postQuestionAnalysisData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'examFilter' => 'nullable|string',
                'typeFilter' => 'nullable|string',
                'difficultyFilter' => 'nullable|string',
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

            // Base query to get questions with response statistics
            $query = Question::with(['exam', 'responses'])
                ->select('questions.*')
                ->addSelect([
                    'total_responses' => ExamResponse::selectRaw('COUNT(*)')
                        ->whereColumn('question_id', 'questions.id'),
                    'correct_responses' => ExamResponse::selectRaw('SUM(is_correct)')
                        ->whereColumn('question_id', 'questions.id')
                ]);

            // Apply filters
            if (($validated['examFilter'] ?? 'all') != 'all') {
                $query->where('exam_id', $validated['examFilter']);
            }

            if (($validated['typeFilter'] ?? 'all') != 'all') {
                $query->where('type', $validated['typeFilter']);
            }

            if (($validated['difficultyFilter'] ?? 'all') != 'all') {
                $query->where('difficulty', $validated['difficultyFilter']);
            }

            // Define sortable columns
            $orderableColumns = [
                'id', 
                'exam_id',
                'question_text',
                'type',
                'difficulty',
                'correct_answer',
                'correct_responses',
                'total_responses'
            ];

            // Apply sorting
            if ($request->has('order') && count($request->input('order'))) {
                $order = $request->input('order.0');
                $columnIndex = $order['column'];
                
                // Map DataTables column index to database column
                $columnMap = [
                    0 => 'id',          // Question ID
                    1 => 'number',      // Number (client-side only)
                    2 => 'exam_id',     // Exam Title
                    3 => 'question_text', // Question Text
                    4 => 'type',        // Type
                    5 => 'difficulty',  // Difficulty
                    6 => 'correct_answer', // Correct Answer
                    7 => 'correct_responses', // Success Rate (sort by correct responses)
                ];
                
                if (isset($columnMap[$columnIndex])) {
                    $columnName = $columnMap[$columnIndex];
                    $query->orderBy($columnName, $order['dir']);
                }
            } else {
                // Default sorting by success rate (descending)
                $query->orderBy('correct_responses', 'desc');
            }

            // Get total count before pagination
            $totalData = $query->count();

            // Pagination
            $start = $validated['start'] ?? 0;
            $length = $validated['length'] ?? 20;
            
            if ($length == -1) {
                $questions = $query->skip($start)->get();
            } else {
                $questions = $query->skip($start)->take($length)->get();
            }

            // Format data for DataTables
            $data = $questions->map(function ($question, $index) use ($start) {
                $totalResponses = $question->total_responses ?? 0;
                $correctResponses = $question->correct_responses ?? 0;
                $incorrectResponses = $totalResponses - $correctResponses;
                
                $correctPercentage = $totalResponses > 0 ? round(($correctResponses / $totalResponses) * 100) : 0;
                $incorrectPercentage = $totalResponses > 0 ? 100 - $correctPercentage : 0;

                return [
                    'number' => $start + $index + 1,
                    'id' => $question->id,
                    'exam' => $question->exam->title ?? 'N/A',
                    'question' => $question->question_text,
                    'type' => $question->type_label,
                    'difficulty' => $question->difficulty_label,
                    'correct_answer' => $question->correct_answer,
                    'success_rate' => $this->getSuccessRateBar($correctPercentage, $incorrectPercentage),
                    'correct_percentage' => $correctPercentage,
                    'action' => $this->getActionButtons($question)
                ];
            });

            return response()->json([
                'draw' => intval($validated['draw']),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalData,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in postQuestionAnalysisData: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while processing the request'
            ], 500);
        }
    }

    private function getSuccessRateBar($correctPercentage, $incorrectPercentage)
    {
        return '<div class="progress" style="height: 25px;">
            <div class="progress-bar bg-success progress-bar-striped" role="progressbar" 
                style="width: '.$correctPercentage.'%" 
                title="Correct: '.$correctPercentage.'%">
                '.$correctPercentage.'%
            </div>
            <div class="progress-bar bg-danger progress-bar-striped" role="progressbar" 
                style="width: '.$incorrectPercentage.'%" 
                title="Incorrect: '.$incorrectPercentage.'%">
                '.$incorrectPercentage.'%
            </div>
        </div>';
    }

    private function getActionButtons($question)
    {
        return '<div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-outline-primary view-details" 
                data-id="'.$question->id.'"
                data-toggle="modal" data-target="#questionDetailsModal">
                <i class="fas fa-chart-bar"></i> Details
            </button>
        </div>';
    }

    public function getQuestionDetails(Request $request)
    {
        $questionId = $request->input('question_id');
        $question = Question::with(['exam', 'responses.examinee'])
            ->withCount(['responses as total_responses', 'responses as correct_responses' => function($query) {
                $query->where('is_correct', true);
            }])
            ->findOrFail($questionId);

        $incorrectResponses = $question->total_responses - $question->correct_responses;
        $correctPercentage = $question->total_responses > 0 ? round(($question->correct_responses / $question->total_responses) * 100) : 0;
        $incorrectPercentage = $question->total_responses > 0 ? 100 - $correctPercentage : 0;

        // Get common incorrect answers
        $commonIncorrect = ExamResponse::where('question_id', $questionId)
            ->where('is_correct', false)
            ->select('response', DB::raw('count(*) as count'))
            ->groupBy('response')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.analysis.question-details', compact(
            'question', 
            'correctPercentage', 
            'incorrectPercentage',
            'commonIncorrect',
            'incorrectResponses'
        ));
    }

}