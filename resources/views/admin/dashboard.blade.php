@extends('layouts.master')

@section('links')

@endsection

@section('css')
<style>
.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}

.info-box-text {
    display: block;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chart-container {
    position: relative;
    height: 400px;
    margin-bottom: 20px;
}

.small-chart {
    height: 300px;
}

.activity-item {
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
}

.activity-item:last-child {
    border-bottom: none;
}
</style>
@endsection

@section('breadcrumb')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content-header -->
@endsection

@section('content')
<div class="container-fluid">
    
    <!-- Key Metrics Row -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-clipboard-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Exams</span>
                    <span class="info-box-number">{{ number_format($totalExams) }}</span>
                    <div class="progress">
                        <div class="progress-bar bg-info" style="width: {{ $activeExams > 0 ? ($activeExams/$totalExams)*100 : 0 }}%"></div>
                    </div>
                    <span class="progress-description">{{ $activeExams }} Active Exams</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Examinees</span>
                    <span class="info-box-number">{{ number_format($totalExaminees) }}</span>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: {{ $completionRate }}%"></div>
                    </div>
                    <span class="progress-description">{{ $completionRate }}% Completion Rate</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Average Score</span>
                    <span class="info-box-number">{{ $averageScore }}%</span>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: {{ $averageScore }}%"></div>
                    </div>
                    <span class="progress-description">
                        @if($averageScore >= 80)
                            Excellent Performance
                        @elseif($averageScore >= 70)
                            Good Performance
                        @elseif($averageScore >= 60)
                            Fair Performance
                        @else
                            Needs Improvement
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-question-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Questions</span>
                    <span class="info-box-number">{{ number_format($totalQuestions) }}</span>
                    <div class="progress">
                        <div class="progress-bar bg-danger" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">Question Bank</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <!-- Monthly Trends Chart -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-1"></i>
                        Monthly Exam Trends
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Exam Status Distribution -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Exam Status Distribution
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row">
        <!-- Score Distribution -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Score Distribution
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container small-chart">
                        <canvas id="scoreDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Question Difficulty Distribution -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-doughnut mr-1"></i>
                        Question Difficulty
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container small-chart">
                        <canvas id="difficultyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Units & Recent Activity -->
    <div class="row">
        <!-- Top Performing Units -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trophy mr-1"></i>
                        Top Performing Units
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="topUnitsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-1"></i>
                        Recent Activity
                    </h3>
                </div>
                <div class="card-body" style="max-height: 460px; overflow-y: auto;">
                    @forelse($recentActivity as $activity)
                        <div class="activity-item">
                            <strong>{{ $activity->examinee->getFullNameAttribute() }}</strong>
                            <br>
                            <small class="text-muted">{{ $activity->exam->title ?? 'N/A' }}</small>
                            <br>
                            <span class="badge badge-{{ $activity->percentage >= 80 ? 'success' : ($activity->percentage >= 60 ? 'warning' : 'danger') }}">
                                {{ $activity->percentage }}%
                            </span>
                            <br>
                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p class="text-muted">No recent activity</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Monthly Trends Chart
    const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    const monthlyData = @json($monthlyTrends);
    
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const trendLabels = monthlyData.map(item => monthNames[item.month - 1] + ' ' + item.year);
    const examCounts = monthlyData.map(item => item.count);
    const avgScores = monthlyData.map(item => parseFloat(item.avg_score || 0));
    
    new Chart(monthlyTrendsCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Exams Completed',
                data: examCounts,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1,
                yAxisID: 'y'
            }, {
                label: 'Average Score (%)',
                data: avgScores,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Number of Exams'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Average Score (%)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
    
    // Status Distribution Pie Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = @json($examResultsByStatus);
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: [
                    '#28a745',
                    '#ffc107', 
                    '#dc3545',
                    '#6c757d',
                    '#17a2b8'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Score Distribution Chart
    const scoreCtx = document.getElementById('scoreDistributionChart').getContext('2d');
    const scoreData = @json($scoreRanges);
    
    new Chart(scoreCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(scoreData),
            datasets: [{
                label: 'Number of Examinees',
                data: Object.values(scoreData),
                backgroundColor: [
                    '#28a745', // 90-100 (green)
                    '#20c997', // 80-89 (teal)
                    '#ffc107', // 70-79 (yellow)
                    '#fd7e14', // 60-69 (orange)
                    '#dc3545'  // Below 60 (red)
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Examinees'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Score Range (%)'
                    }
                }
            }
        }
    });
    
    // Question Difficulty Chart
    const difficultyCtx = document.getElementById('difficultyChart').getContext('2d');
    const difficultyData = @json($questionDifficulty);
    
    const difficultyLabels = {
        'easy': 'Easy',
        'moderate': 'Moderate', 
        'difficult': 'Difficult',
        'extra_difficult': 'Extra Difficult'
    };
    
    const difficultyColors = {
        'easy': '#28a745',
        'moderate': '#ffc107',
        'difficult': '#fd7e14', 
        'extra_difficult': '#dc3545'
    };
    
    new Chart(difficultyCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(difficultyData).map(key => difficultyLabels[key] || key),
            datasets: [{
                data: Object.values(difficultyData),
                backgroundColor: Object.keys(difficultyData).map(key => difficultyColors[key] || '#6c757d')
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Top Units Chart
    const unitsCtx = document.getElementById('topUnitsChart').getContext('2d');
    const unitsData = @json($topUnits);
    
    if (unitsData && unitsData.length > 0) {
        new Chart(unitsCtx, {
            type: 'bar',
            data: {
                labels: unitsData.map(unit => unit.unit_name || ('Unit ' + unit.unit)),
                datasets: [{
                    label: 'Average Score (%)',
                    data: unitsData.map(unit => parseFloat(unit.avg_score).toFixed(2)),
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Average Score (%)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                const dataIndex = context.dataIndex;
                                const examCount = unitsData[dataIndex].exam_count;
                                return 'Exams: ' + examCount;
                            }
                        }
                    }
                }
            }
        });
    } else {
        // Display message when no data is available
        unitsCtx.canvas.parentElement.innerHTML = '<div class="text-center text-muted p-4"><i class="fas fa-chart-bar fa-3x mb-3"></i><br>No unit performance data available yet.</div>';
    }
});
</script>
@endsection