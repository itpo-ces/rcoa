<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Examinee;
use App\Models\ExamResult;
use App\Models\Question;
use App\Models\ExamResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get key metrics
        $totalExams = Exam::count();
        $activeExams = Exam::where('is_active', true)->count();
        $totalExaminees = Examinee::count();
        $totalQuestions = Question::count();
        
        // Get recent exam completion rate
        $completedExams = ExamResult::count();
        $completionRate = $totalExaminees > 0 ? round(($completedExams / $totalExaminees) * 100, 2) : 0;
        
        // Get average score
        $averageScore = ExamResult::avg('percentage') ?? 0;
        $averageScore = round($averageScore, 2);
        
        // Get exam results by status for pie chart
        $examResultsByStatus = ExamResult::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Get monthly exam completion trends (last 12 months)
        $monthlyTrends = ExamResult::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(percentage) as avg_score')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Get question difficulty distribution
        $questionDifficulty = Question::select('difficulty', DB::raw('count(*) as count'))
            ->groupBy('difficulty')
            ->pluck('count', 'difficulty')
            ->toArray();
        
        // Get top performing units (adjusted for better data display)
        $topUnits = Examinee::join('exam_results', 'examinees.id', '=', 'exam_results.examinee_id')
            ->leftJoin('ref_units', 'examinees.unit', '=', 'ref_units.UnitId')
            ->select(
                'examinees.unit', 
                'ref_units.OrderNumberPrefix as unit_name',
                DB::raw('AVG(exam_results.percentage) as avg_score'), 
                DB::raw('COUNT(*) as exam_count')
            )
            ->whereNotNull('examinees.unit')
            ->groupBy('examinees.unit', 'ref_units.OrderNumberPrefix')
            ->having('exam_count', '>=', 1) // Reduced threshold to show more data
            ->orderBy('avg_score', 'desc')
            ->limit(10)
            ->get();
        
        // Fallback: If no units found, get all units with any exam results
        if ($topUnits->isEmpty()) {
            $topUnits = Examinee::join('exam_results', 'examinees.id', '=', 'exam_results.examinee_id')
                ->select(
                    'examinees.unit', 
                    DB::raw('AVG(exam_results.percentage) as avg_score'), 
                    DB::raw('COUNT(*) as exam_count')
                )
                ->whereNotNull('examinees.unit')
                ->groupBy('examinees.unit')
                ->orderBy('avg_score', 'desc')
                ->limit(10)
                ->get();
        }
        
        // Get recent activity (last 7 days)
        $recentActivity = ExamResult::with(['examinee', 'exam'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get score distribution ranges
        $scoreRanges = [
            '90-100' => ExamResult::whereBetween('percentage', [90, 100])->count(),
            '80-89' => ExamResult::whereBetween('percentage', [80, 89.99])->count(),
            '70-79' => ExamResult::whereBetween('percentage', [70, 79.99])->count(),
            '60-69' => ExamResult::whereBetween('percentage', [60, 69.99])->count(),
            'Below 60' => ExamResult::where('percentage', '<', 60)->count(),
        ];
        
        return view('admin.dashboard', compact(
            'totalExams',
            'activeExams', 
            'totalExaminees',
            'totalQuestions',
            'completionRate',
            'averageScore',
            'examResultsByStatus',
            'monthlyTrends',
            'questionDifficulty',
            'topUnits',
            'recentActivity',
            'scoreRanges'
        ));
    }
}