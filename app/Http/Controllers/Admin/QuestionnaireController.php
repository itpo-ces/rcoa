<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Question;
use App\Models\Exam;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $exams = Exam::all();
        $types = Question::getEnumValues('type');
        $difficulties = Question::getEnumValues('difficulty');
        
        return view('admin.questionnaire', compact('exams', 'types', 'difficulties'));
    }

    public function postQuestionnaireData(Request $request)
    {
        try{
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'examFilter' => 'nullable|string',
                'difficultyFilter' => 'nullable|string',
                'typeFilter' => 'nullable|string',
                'start' => 'integer|min:0',
                'length' => 'integer|min:1|max:1000',
                'draw' => 'required|integer',
                'order' => 'nullable|array',
                'order.0.column' => 'nullable|integer',
                'order.0.dir' => 'nullable|in:asc,desc'
            ]);

            // Handle validation errors
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get the validated data
            $validated = $validator->validated();

            // Set memory limit and timeout for large queries
            ini_set('memory_limit', '512M');
            DB::statement('SET SESSION wait_timeout=120');
            set_time_limit(120);

            // Query the database
            $query = Question::query();

            // Apply Filters
            if (($validated['examFilter'] ?? 'all') != 'all') {
                $query->where('exam_id', $validated['examFilter']);
            }

            if (($validated['typeFilter'] ?? 'all') != 'all') {
                $query->where('type', $validated['typeFilter']);
            }

            if (($validated['difficultyFilter'] ?? 'all') != 'all') {
                $query->where('difficulty', $validated['difficultyFilter']);
            }

            // Sorting
            $orderableColumns = ['id', 'exam_id', 'question_text', 'type', 'difficulty', 'correct_answer', 'options', 'is_active', 'created_at', 'updated_at'];

            if ($request->has('order') && count($request->input('order'))) {
                $order = $request->input('order.0');
                if (isset($orderableColumns[$order['column']])) {
                    $query->orderBy($orderableColumns[$order['column']], $order['dir']);
                }
            } else {
                $query->orderBy('id', 'desc')->orderBy('created_at', 'desc');
            }

            // Pagination and response
            $totalData = $query->count();

            // Handle pagination - if length is -1 (All records), don't apply limit
            $start = $validated['start'] ?? 0;
            $length = $validated['length'] ?? 20;
            
            if ($length == -1) {
                $data = $query->skip($start)->get();
            } else {
                $data = $query->skip($start)->take($length)->get();
            }

            // Select only necessary columns to reduce memory usage
            $query->select([
                'id', 'exam_id', 'question_text', 'type', 'difficulty', 'correct_answer', 'options', 'is_active', 'created_at', 'updated_at'
            ]);

            // Always sort by id to ensure consistent pagination
            $query->orderBy('id');

            // Check if current page has exactly one result
            $isSingleResult = ($data->count() === 1);

            return response()->json([
                'draw' => intval($validated['draw']),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalData,
                'data' => $data->map(function ($item) use ($isSingleResult){
                    // Format the status
                    if($item->is_active) {
                        $status = '<span class="badge badge-success">Active</span>';
                    } else {
                        $status = '<span class="badge badge-secondary">Inactive</span>';
                    }

                    // Normalize options into array
                    if (is_array($item->options)) {
                        $decoded = $item->options; // already an array
                    } elseif (is_string($item->options)) {
                        $decoded = json_decode($item->options, true);

                        // If it's a string containing JSON, decode again
                        if (is_string($decoded)) {
                            $decoded = json_decode($decoded, true);
                        }
                    } else {
                        $decoded = [];
                    }
                    
                    $itemData = [
                        'id' => $item->id,
                        'exam_id' => $item->exam_id,
                        'question_text' => $item->question_text,
                        'type' => $item->type,
                        'difficulty' => $item->difficulty,
                        'correct_answer' => $item->correct_answer,
                        // 'options' => $item->options ? json_encode(json_decode($item->options, true), JSON_PRETTY_PRINT) : '[]',
                        'options' => $decoded ? json_encode($decoded, JSON_PRETTY_PRINT) : '[]',
                        'created_at' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : null,
                        'updated_at' => $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : null,
                        'is_active' => $item->is_active
                    ];

                    $jsonItemData = htmlspecialchars(json_encode($itemData), ENT_QUOTES, 'UTF-8');

                    // Action HTML - different depending on if we have a single result
                    $action = '';
                    
                    if ($isSingleResult) {
                        // For single result, show buttons in a row
                        $action = '<div class="btn-group btn-group-sm">';
                        
                        // Edit button - only show if is_active = 1
                        if ($item->is_active == 1) {
                            $action .= '<button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#edit_item_modal" 
                                    data-item="' . $jsonItemData . '">
                                    <i class="fas fa-edit"></i> Edit
                                    </button>';
                        }
                        
                        // Delete button - only show if is_active = 1
                        if ($item->is_active == 1) {
                            $action .= '<button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#delete_item_modal" 
                                    data-item="' . $jsonItemData . '">
                                    <i class="fas fa-trash-alt"></i> Delete
                                    </button>';
                        }
                        
                        // Restore button - only show if is_active = 0
                        if ($item->is_active == 0) {
                            $action .= '<button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#restore_item_modal" 
                                    data-item="' . $jsonItemData . '">
                                    <i class="fas fa-undo"></i> Restore
                                    </button>';
                        }
                        
                        $action .= '</div>';
                    } else {
                        // Standard dropdown for multiple results
                        $action = '<div class="dropdown btn-group">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="actionDropdown' . $item->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionDropdown' . $item->id . '">';

                        // Edit button - only show if is_active = 1
                        if ($item->is_active == 1) {
                            $action .= '<a class="dropdown-item" href="#" data-toggle="modal" data-target="#edit_item_modal" 
                                        data-item="' . $jsonItemData . '">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                        </a>';
                        }

                        // Delete button - only show if is_active = 1
                        if ($item->is_active == 1) {
                            $action .= '<a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#delete_item_modal" 
                                        data-item="' . $jsonItemData . '">
                                        <i class="fas fa-trash-alt mr-2"></i>Delete
                                        </a>';
                        }

                        // Restore button - only show if is_active = 0
                        if ($item->is_active == 0) {
                            $action .= '<a class="dropdown-item text-warning" href="#" data-toggle="modal" data-target="#restore_item_modal" 
                                        data-item="' . $jsonItemData . '">
                                        <i class="fas fa-undo mr-2"></i>Restore
                                        </a>';
                        }

                        $action .= '</div></div>';
                    }

                    // Return the formatted data
                    return [
                        'id' => $item->id,
                        'exam' => $item->exam->title ?? 'None',
                        'question' => $item->question_text,
                        'type' => $item->type_label,
                        'difficulty' => $item->difficulty_label,
                        'answer' => $item->correct_answer,
                        'options' => $decoded ? implode(', ', $decoded) : 'None',
                        'status' => $status,
                        'action' => $action
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in postMobilityData: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'message' => 'An error occurred while processing the request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'exam_id' => 'required|exists:exams,id',
                'question_text' => 'required|string|max:1000',
                'type' => 'required|in:multiple_choice,fill_in_the_blanks,true_or_false,yes_or_no',
                'difficulty' => 'required|in:easy,moderate,difficult,extra_difficult',
                'correct_answer' => 'required|string|max:255',
                'options' => 'required_if:type,multiple_choice|json',
            ], [
                'exam_id.required' => 'Please select an exam.',
                'question_text.required' => 'The question text is required.',
                'type.required' => 'Please select a question type.',
                'difficulty.required' => 'Please select a difficulty level.',
                'correct_answer.required' => 'The correct answer is required.',
                'options.required_if' => 'Options are required for multiple choice questions.',
                'options.json' => 'Options must be in valid JSON format.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Parse JSON options if provided
            $validated = $validator->validated();
            
            if (isset($validated['options']) && is_string($validated['options'])) {
                try {
                    $options = json_decode($validated['options'], true, 512, JSON_THROW_ON_ERROR);
                    $validated['options'] = $options;
                } catch (\JsonException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid JSON format for options',
                        'errors' => ['options' => ['The options field must be valid JSON.']]
                    ], 422);
                }
            }

            // Create the question
            $question = Question::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Question created successfully',
                'data' => $question
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Question creation error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the question.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:questions,id',
                'exam_id' => 'required|exists:exams,id',
                'question_text' => 'required|string|max:1000',
                'type' => 'required|in:multiple_choice,fill_in_the_blanks,true_or_false,yes_or_no',
                'difficulty' => 'required|in:easy,moderate,difficult,extra_difficult',
                'correct_answer' => 'required|string|max:255',
                'options' => 'required_if:type,multiple_choice|json',
            ], [
                'exam_id.required' => 'Please select an exam.',
                'question_text.required' => 'The question text is required.',
                'type.required' => 'Please select a question type.',
                'difficulty.required' => 'Please select a difficulty level.',
                'correct_answer.required' => 'The correct answer is required.',
                'options.required_if' => 'Options are required for multiple choice questions.',
                'options.json' => 'Options must be in valid JSON format.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Parse JSON options if provided
            $validated = $validator->validated();
            
            if (isset($validated['options']) && is_string($validated['options'])) {
                try {
                    $options = json_decode($validated['options'], true, 512, JSON_THROW_ON_ERROR);
                    $validated['options'] = $options;
                } catch (\JsonException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid JSON format for options',
                        'errors' => ['options' => ['The options field must be valid JSON.']]
                    ], 422);
                }
            }

            // Find and update
        $question = Question::findOrFail($validated['id']);
        $question->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully',
                'data' => $question
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Question update error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the question.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:questions,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            
            // Find the question and delete it
            $question = Question::findOrFail($validated['id']);
            $question->update(['is_active' => 0]);
            
            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Question deletion error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the question.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function restore(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:questions,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            
            // Find the question and restore it
            $question = Question::findOrFail($validated['id']);
            $question->update(['is_active' => 1]);
            
            return response()->json([
                'success' => true,
                'message' => 'Question restored successfully',
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Question restoration error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while restoring the question.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $failedRecords = [];
            $successCount = 0;
            $skippedCount = 0;
            $skippedQuestions = [];

            // // Fetch existing questions for duplicate checking (if needed)
            // $existingQuestions = Question::pluck('question_text')->map(function ($text) {
            //     return strtolower(trim($text));
            // })->toArray();

            $exams = Exam::pluck('title', 'id')->toArray();

            foreach ($request->data as $row) {
                try {
                    // Prepare the skipped row structure
                    $skippedRow = [
                        'exam_id' => $row['exam_id'] ?? null,
                        'exam_title' => $exams[$row['exam_id']] ?? '',
                        'question_text' => $row['question_text'] ?? '',
                        'type' => $row['type'] ?? '',
                        'difficulty' => $row['difficulty'] ?? '',
                        'correct_answer' => $row['correct_answer'] ?? '',
                        'options' => $row['options'] ?? '',
                        'reason' => '',
                    ];

                    // // Skip if question already exists (optional)
                    // $questionText = strtolower(trim($row['question_text'] ?? ''));
                    // if (in_array($questionText, $existingQuestions)) {
                    //     $skippedRow['reason'] = 'Duplicate question';
                    //     $skippedQuestions[] = $skippedRow;
                    //     $skippedCount++;
                    //     continue;
                    // }

                    // Validate row
                    // $validator = Validator::make($row, [
                    //     'exam_id' => 'required|exists:exams,id',
                    //     'question_text' => 'required|string|max:1000',
                    //     'type' => 'required|in:multiple_choice,fill_in_the_blanks,true_or_false,yes_or_no',
                    //     'difficulty' => 'required|in:easy,moderate,difficult,extra_difficult',
                    //     'correct_answer' => 'required|string|max:255',
                    //     'options' => 'required_if:type,multiple_choice|json',
                    // ]);

                    $rules = [
                        'exam_id' => 'required|exists:exams,id',
                        'question_text' => 'required|string|max:1000',
                        'type' => 'required|in:multiple_choice,fill_in_the_blanks,true_or_false,yes_or_no',
                        'difficulty' => 'required|in:easy,moderate,difficult,extra_difficult',
                        'correct_answer' => 'required|string|max:255',
                    ];

                    // Only add JSON validation if type is multiple_choice
                    if ($row['type'] === 'multiple_choice') {
                        $rules['options'] = 'required|json';
                    } else {
                        $rules['options'] = 'nullable';
                    }

                    $validator = Validator::make($row, $rules);

                    if ($validator->fails()) {
                        $skippedRow['reason'] = 'Validation failed: ' . implode(', ', $validator->errors()->all());
                        $skippedQuestions[] = $skippedRow;
                        $skippedCount++;
                        
                        $failedRecords[] = [
                            'row' => $row,
                            'errors' => $validator->errors()->all(),
                        ];
                        continue;
                    }

                    // Parse JSON options if provided
                    $validated = $validator->validated();
                    
                    if (isset($validated['options']) && is_string($validated['options'])) {
                        try {
                            $options = json_decode($validated['options'], true, 512, JSON_THROW_ON_ERROR);
                            $validated['options'] = $options;
                        } catch (\JsonException $e) {
                            $skippedRow['reason'] = 'Invalid JSON format for options';
                            $skippedQuestions[] = $skippedRow;
                            $skippedCount++;
                            continue;
                        }
                    }

                    // Create the question
                    Question::create($validated);
                    $successCount++;
                    // $existingQuestions[] = $questionText; // Add to existing questions
                } catch (\Exception $e) {
                    $skippedRow['reason'] = 'Exception: ' . $e->getMessage();
                    $skippedQuestions[] = $skippedRow;
                    $skippedCount++;
                    
                    $failedRecords[] = [
                        'row' => $row,
                        'errors' => [$e->getMessage()],
                    ];
                }
            }

            DB::commit();

            // Construct appropriate success message
            $message = "";
            if ($successCount > 0) {
                $message .= "Successfully imported {$successCount} questions. ";
            }
            if ($skippedCount > 0) {
                $message .= "{$skippedCount} questions were skipped. ";
            }
            if (!empty($failedRecords)) {
                $message .= count($failedRecords) . " questions failed to import.";
            }

            if (empty($message)) {
                $message = "No new questions were imported.";
            }

            return response()->json([
                'success' => true,
                'successCount' => $successCount,
                'skippedCount' => $skippedCount,
                'failedRecords' => $failedRecords,
                'skippedQuestions' => $skippedQuestions,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while importing questions: ' . $e->getMessage()
            ], 500);
        }
    }


}
