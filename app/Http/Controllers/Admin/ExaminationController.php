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
                
                return [
                    'number' => $start + $index + 1,
                    'id' => $result->id,
                    'title' => $result->title ?? 'N/A',
                    'exam_date' => $result->exam_date ? $result->exam_date->format('F j, Y') : 'None',
                    'start_time' => $result->start_time ? $result->start_time->format('g:i A') : 'None',
                    'end_time' => $result->end_time ? $result->end_time->format('g:i A') : 'None',
                    'duration' => $result->duration_minutes ? $result->duration_minutes . ' minutes' : 'None',
                    'no_of_questions' => $result->number_of_questions,
                    'status' => 'Active',
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
}
