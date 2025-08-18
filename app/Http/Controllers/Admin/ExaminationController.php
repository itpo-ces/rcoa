<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Exam;

class ExaminationController extends Controller
{
    public function index()
    {
        $exams = Exam::all();
        return view('admin.examination', compact('exams'));
    }

    public function postExaminationData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'examFilter' => 'nullable|string',
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

            // Get the validated data
            $validated = $validator->validated();

            // Set memory limit and timeout for large queries
            ini_set('memory_limit', '512M');
            DB::statement('SET SESSION wait_timeout=120');
            set_time_limit(120);

            // Query the database
            $query = Exam::query();

            // Apply filters
            if (($validated['examFilter'] ?? 'all') != 'all') {
                $query->where('id', $validated['examFilter']);
            }

            // Get total count before pagination
            $totalData = $query->count();

            // Pagination
            $start = $validated['start'] ?? 0;
            $length = $validated['length'] ?? 20;
            
            if ($length == -1) {
                $results = $query->skip($start)->get();
            } else {
                $results = $query->skip($start)->take($length)->get();
            }

            // Format data for DataTables
            $data = $results->map(function ($result, $index) use ($start) {
                
            // Format the status
            if($result->is_active == 1) {
                $status = 'Active';
            } else {
                $status = 'Inactive';
            }

                return [
                    'number' => $start + $index + 1,
                    'id' => $result->id,
                    'title' => $result->title ?? 'N/A',
                    'exam_date' => $result->exam_date ? $result->exam_date->format('F j, Y') : 'None',
                    'start_time' => $result->start_time ? $result->start_time->format('g:i A') : 'None',
                    'end_time' => $result->end_time ? $result->end_time->format('g:i A') : 'None',
                    'duration' => $result->duration_minutes ? $result->duration_minutes . ' minutes' : 'None',
                    'no_of_questions' => $result->number_of_questions,
                    'status' => $status,
                    'action' => $this->getActionButtons($result)
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
        // Convert the result to an array
        $itemData = [
            'id' => $result->id,
            'title' => $result->title,
            'exam_date' => $result->exam_date ? $result->exam_date->format('Y-m-d') : '',
            'start_time' => $result->start_time ? $result->start_time->format('H:i') : '',
            'end_time' => $result->end_time ? $result->end_time->format('H:i') : '',
            'duration_minutes' => $result->duration_minutes,
            'number_of_questions' => $result->number_of_questions,
        ];

        // Encode to JSON and escape for HTML
        $jsonItemData = htmlspecialchars(json_encode($itemData), ENT_QUOTES, 'UTF-8');

        if ($result->is_active == 1) {
            return '<div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#edit_item_modal" data-item=\'' . $jsonItemData . '\'>
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#delete_item_modal" data-item=\'' . $jsonItemData . '\'>
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </div>';
        } else {
            return '<div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#restore_item_modal" data-item=\'' . $jsonItemData . '\'>
                    <i class="fas fa-undo"></i> Restore
                </button>
            </div>';
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'exam_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'duration_minutes' => 'required|integer|min:1',
                'number_of_questions' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Create the exam
            $exam = Exam::create([
                'title' => $validated['title'],
                'exam_date' => $validated['exam_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration_minutes' => $validated['duration_minutes'],
                'number_of_questions' => $validated['number_of_questions'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exam created successfully',
                'exam' => $exam
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Exam creation error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the exam'
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:exams,id',
                'title' => 'required|string|max:255',
                'exam_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'duration_minutes' => 'required|integer|min:1',
                'number_of_questions' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Find the exam and update it
            $exam = Exam::findOrFail($validated['id']);
            $exam->update([
                'title' => $validated['title'],
                'exam_date' => $validated['exam_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration_minutes' => $validated['duration_minutes'],
                'number_of_questions' => $validated['number_of_questions'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exam updated successfully',
                'exam' => $exam
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Exam update error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the exam'
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:exams,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Find the exam and update is_active to 0
            $exam = Exam::findOrFail($validated['id']);
            $exam->update(['is_active' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Exam deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Exam deletion error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the exam'
            ], 500);
        }
    }

    public function restore(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:exams,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Find the exam and update is_active to 1
            $exam = Exam::findOrFail($validated['id']);
            $exam->update(['is_active' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'Exam restored successfully',
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Exam restoration error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while restoring the exam'
            ], 500);
        }
    }
}
