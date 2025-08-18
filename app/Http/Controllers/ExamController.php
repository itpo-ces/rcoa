<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Token;
use App\Models\Examinee;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamResponse;
use App\Models\Unit;
use App\Models\Subunit;
use App\Models\Station;
use App\Models\ExamResult;

class ExamController extends Controller
{
    public function start()
    {
        // Check if exam was started and time has expired
        if (session()->has('exam_started')) {
            $remainingTime = max(0, strtotime(session('exam_end_time')) - time());
            if ($remainingTime <= 0) {
                return redirect()->route('exam.certification');
            }
        }
        // Get examinee if token exists in session
        $examinee = null;
        if (session()->has('token_id')) {
            $examinee = Examinee::where('token_id', session('token_id'))->first();
        }
        return view('exam.start', compact('examinee'));
    }
    
    public function validateToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $token = Token::where('token', $request->token)
                        ->where(function($query) {
                            $query->where('status', Token::STATUS_AVAILABLE)
                                ->orWhere(function($q) {
                                    // Allow reuse if same session
                                    $q->where('status', Token::STATUS_IN_USE)
                                        ->where('id', session('token_id'));
                                });
                        })
                        ->first();

            if (!$token) {
                return back()->with('error', 'Invalid token or token already in use by another examinee.');
            }

            session(['valid_token' => true, 'token_id' => $token->id]);

            // Update status if not already in use
            if ($token->status !== Token::STATUS_IN_USE) {
                $token->update(['status' => Token::STATUS_IN_USE]);
            }

            return redirect()->route('exam.privacy');
        } catch (\Exception $e) {
            \Log::error('Token validation failed: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while validating the token.');
        }
    }

    public function checkTokenStatus($tokenId)
    {
        $token = Token::find($tokenId);
        
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }
        
        return response()->json([
            'status' => $token->status,
            'is_used' => $token->is_used,
            'last_activity' => $token->updated_at
        ]);
    }

    public function privacy()
    {
        // Check if exam was started and time has expired
        if (session()->has('exam_started')) {
            $remainingTime = max(0, strtotime(session('exam_end_time')) - time());
            if ($remainingTime <= 0) {
                return redirect()->route('exam.certification');
            }
        }

        return view('exam.privacy');
    }
    
    public function acceptPrivacy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'accept' => 'required|accepted'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Ensure token_id exists in session
            if (!session()->has('token_id')) {
                return back()->with('error', 'Session expired or invalid. Please enter your token again.');
            }

            Examinee::updateOrCreate(
                ['token_id' => session('token_id')],
                [
                    'accepted_privacy' => true,
                    'rank' => 'Temporary',       // Default placeholder values
                    'first_name' => 'Temporary',
                    'last_name' => 'Temporary',
                    'designation' => 'Temporary',
                    'unit' => 'Temporary',
                    'subunit' => 'Temporary'
                ]
            );

            return redirect()->route('exam.register');

        } catch (\Exception $e) {
            \Log::error('Accept Privacy Error: ' . $e->getMessage());

            return back()->with('error', 'An error occurred while accepting the privacy agreement. Please try again.');
        }
    }

    public function register()
    {
        // Check if exam was started and time has expired
        if (session()->has('exam_started')) {
            $remainingTime = max(0, strtotime(session('exam_end_time')) - time());
            if ($remainingTime <= 0) {
                return redirect()->route('exam.certification');
            }
        }
        // $units = Unit::where('IsActive', '=', '1')
        //             ->where('isRegionalBased', 1)->get();
        
        $units = Unit::where(function($query) {
                $query->where('IsActive', '=', '1')
                    ->where('isRegionalBased', 1);
            })
            ->orWhere('UnitId', 29)
            ->get();

        return view('exam.register', compact('units'));
    }
    
    public function storeRegistration(Request $request)
    {
         $validated = $request->validate([
            'rank' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'qualifier' => 'nullable|string',
            'designation' => 'required|string',
            'manual_designation' => 'required_if:designation,Others|nullable|string',
            'unit' => 'required|string',
            'subunit' => 'nullable|string',
            'station' => 'nullable|string',
        ]);

        try {
            // If designation is "Others", use the manual_designation value
            if ($validated['designation'] === 'Others') {
                $validated['designation'] = $validated['manual_designation'];
            }
            // Ensure token_id exists in session
            if (!session()->has('token_id')) {
                return back()->with('error', 'Session expired or invalid. Please enter your token again.');
            }

            // Save or update examinee data
            Examinee::updateOrCreate(
                ['token_id' => session('token_id')],
                $validated
            );

            // Get the examinee's information
            $examinee = Examinee::where('token_id', session('token_id'))->first();

            if (!$examinee) {
                return redirect()->back()->with('error', 'Examinee registration failed. Please try again.');
            }

            // Construct full name
            $fullName = strtoupper(trim("{$examinee->rank} {$examinee->first_name} {$examinee->middle_name} {$examinee->last_name} {$examinee->qualifier}"));

            // Store in session
            session([
                'examinee_full_name' => $fullName
            ]);

            return redirect()->route('exam.instructions');
        } catch (\Exception $e) {
            \Log::error('Store Registration Error: ' . $e->getMessage());

            return back()->with('error', 'An error occurred during registration. Please try again.');
        }
    }

    public function instructions()
    {
        // Check if time has expired
        if (session()->has('exam_started')) {
            $remainingTime = max(0, strtotime(session('exam_end_time')) - time());
            if ($remainingTime <= 0) {
                return redirect()->route('exam.certification');
            }
        }

        return view('exam.instructions');
    }

    public function startExam(Request $request)
    {
        try {
            // Validate certification
            if (!$request->has('certified') || $request->certified !== '1') {
                return back()->with('error', 'You must certify the statement before starting the exam.');
            }
            
            $exam = Exam::first();

            if (!$exam) {
                return back()->with('error', 'Exam is not available. Please contact the administrator.');
            }

            $questionLimit = $exam->number_of_questions ?? 50;

            // Question type distribution
            $typeDistribution = [
                'multiple_choice' => 0.60,
                'fill_in_the_blanks' => 0.20,
                'true_or_false' => 0.15,
                'yes_or_no' => 0.05
            ];

            // Difficulty distribution
            $difficultyDistribution = [
                'easy' => 0.50,
                'moderate' => 0.30,
                'difficult' => 0.15,
                'extra_difficult' => 0.05
            ];

            // First get all active questions grouped by type and difficulty
            $allQuestions = $exam->questions()
                ->where('is_active', 1)
                ->get()
                ->groupBy(['type', 'difficulty']);

            $selectedQuestions = collect();
            $targetCounts = [];

            // Calculate target counts for each category without rounding
            foreach ($typeDistribution as $type => $typePercent) {
                foreach ($difficultyDistribution as $difficulty => $diffPercent) {
                    $targetCounts[$type][$difficulty] = $questionLimit * $typePercent * $diffPercent;
                }
            }

            // First pass - floor all counts
            foreach ($targetCounts as $type => $difficulties) {
                foreach ($difficulties as $difficulty => $count) {
                    $toTake = floor($count);
                    $available = $allQuestions->get($type, collect())
                        ->get($difficulty, collect())
                        ->shuffle();

                    if ($available->count() < $toTake) {
                        \Log::warning("Not enough {$type} - {$difficulty} questions. Needed {$toTake}, got {$available->count()}.");
                    }

                    $questions = $available->take($toTake);
                    $selectedQuestions = $selectedQuestions->concat($questions);
                }
            }

            // Calculate remaining questions needed
            $remaining = $questionLimit - $selectedQuestions->count();

            if ($remaining > 0) {
                // Second pass - distribute remaining questions proportionally
                $flattenedTargets = [];
                foreach ($targetCounts as $type => $difficulties) {
                    foreach ($difficulties as $difficulty => $count) {
                        $fraction = $count - floor($count);
                        $flattenedTargets[] = [
                            'type' => $type,
                            'difficulty' => $difficulty,
                            'fraction' => $fraction
                        ];
                    }
                }

                // Sort by largest fraction first
                usort($flattenedTargets, function ($a, $b) {
                    return $b['fraction'] <=> $a['fraction'];
                });

                foreach ($flattenedTargets as $target) {
                    if ($remaining <= 0) break;

                    $available = $allQuestions->get($target['type'], collect())
                        ->get($target['difficulty'], collect())
                        ->shuffle();

                    // Pick one unique question if possible
                    $extra = $available->first(function ($q) use ($selectedQuestions) {
                        return !$selectedQuestions->contains('id', $q->id);
                    });

                    if ($extra) {
                        $selectedQuestions->push($extra);
                        $remaining--;
                    }
                }
            }

            // Final balancing loop (fill-up from ANY active question if still short)
            while ($remaining > 0) {
                $extra = $exam->questions()
                    ->where('is_active', 1)
                    ->inRandomOrder()
                    ->first();

                if ($extra && !$selectedQuestions->contains('id', $extra->id)) {
                    $selectedQuestions->push($extra);
                    $remaining--;
                } else {
                    break; // no more unique questions available
                }
            }

            // Final shuffle
            $questions = $selectedQuestions->shuffle();

            if ($questions->isEmpty()) {
                return back()->with('error', 'No questions found for this exam.');
            }

            // Initialize exam progress tracking
            $examinee = Examinee::where('token_id', session('token_id'))->first();
            $examinee->update([
                'last_question_number' => 1, // Start at question 1
                'exam_progress' => [
                    'questions' => $questions->pluck('id')->toArray(),
                    'answers' => []
                ]
            ]);
            
            // Set session data
            session([
                'exam_started' => true,
                'exam_start_time' => now()->toDateTimeString(),
                'exam_end_time' => now()->addMinutes($exam->duration_minutes)->toDateTimeString(),
                'questions' => $questions->pluck('id')->toArray(),
                'current_question' => 0,
                'answers' => []
            ]);

            return redirect()->route('exam.question', ['question_number' => 1]);
        } catch (\Exception $e) {
            \Log::error('startExam Error: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred while starting the exam. Please try again.');
        }
    }

    public function showQuestion($question_number)
    {
        try {
            // Check if we need to resume exam
            if (!session()->has('exam_started') && session()->has('token_id')) {
                return $this->resumeExam();
            }

            // Validate exam session
            if (!session()->has('exam_started')) {
                return redirect()->route('exam.instructions')->with('error', 'Please start the exam first.');
            }

            // Validate question number
            if ($question_number < 1 || $question_number > count(session('questions', []))) {
                abort(404);
            }

            // Update current question in session (0-based index)
            session(['current_question' => $question_number - 1]);

            // Update examinee's last question
            $examinee = Examinee::where('token_id', session('token_id'))->first();
            if ($examinee) {
                $examinee->update([
                    'last_question_number' => $question_number,
                    'exam_progress' => [
                        'questions' => session('questions'),
                        'answers' => session('answers', [])
                    ]
                ]);
            }

            // Check remaining time
            $remainingTime = max(0, strtotime(session('exam_end_time')) - time());
            if ($remainingTime <= 0) {
                return redirect()->route('exam.certification');
            }

            // Get current question
            $question = Question::find(session('questions')[$question_number - 1]);

            if (!$question) {
                return redirect()->route('exam.instructions')->with('error', 'Question not found.');
            }

            return view('exam.question', [
                'question' => $question,
                'question_number' => $question_number,
                'total_questions' => count(session('questions')),
                'remaining_time' => $remainingTime
            ]);
        } catch (\Exception $e) {
            \Log::error('Show Question Error: ' . $e->getMessage(), [
                'exception' => $e,
                'session_data' => session()->all(),
                'question_number' => $question_number ?? null
            ]);
            return redirect()->route('exam.instructions')
                ->with('error', 'An error occurred while loading the question. Please try again.');
        }
    }
    
    public function saveAnswer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'question_id' => 'required|exists:questions,id',
                'answer' => 'required',
                'current_question' => 'sometimes|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            // Save answer in session
            $answers = session('answers', []);
            $answers[$request->question_id] = $request->answer;
            session(['answers' => $answers]);

            // Get current question number
            $currentQuestionNumber = (int)($request->current_question ?? session('current_question', 0) + 1);

            // Update examinee progress
            $examinee = Examinee::where('token_id', session('token_id'))->first();
            if ($examinee) {
                $examinee->update([
                    'last_question_number' => $currentQuestionNumber,
                    'exam_progress' => [
                        'questions' => session('questions'),
                        'answers' => $answers
                    ]
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Save Answer Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['An unexpected error occurred while saving the answer. Please try again.']
            ], 500);
        }
    }

    public function submitExam(Request $request)
    {
        try {
            // If exam is being submitted but wasn't properly started
            if (!session()->has('token_id')) {
                return redirect()->route('exam.start')->with('error', 'Invalid exam session.');
            }

            // If token was somehow marked as used already
            $token = Token::find(session('token_id'));
            if ($token->status === Token::STATUS_USED) {
                return redirect()->route('exam.start')->with('error', 'This token has already been used.');
            }

            // If this is a GET request (from time expiration), show certification form
            if ($request->isMethod('get')) {
                return redirect()->route('exam.certification');
            }

            // If exam already submitted, redirect to results
            if (session()->has('exam_score')) {
                return redirect()->route('exam.results');
            }

            $request->validate([
                'certify' => 'required|accepted'
            ]);

            $exam = Exam::first();
            $questions = Question::whereIn('id', session('questions'))->get();
            $answers = session('answers', []);
            $examinee = Examinee::where('token_id', session('token_id'))->first();

            if (!$examinee) {
                return redirect()->route('exam.instructions')->with('error', 'Examinee not found. Please try again.');
            }

            $examinee->update([
                'accepted_certification' => true,
                'last_question_number' => null,
                'exam_progress' => null
            ]);

            if (!$questions->count()) {
                // If no questions, still create empty responses and show results
                $questions = Question::whereIn('id', session('questions', []))->get();
            }

            $score = 0;

            foreach ($questions as $question) {
                $userAnswer = $answers[$question->id] ?? null;
                $isCorrect = false;

                // Record response even if empty
                if ($userAnswer !== null) {
                    $isCorrect = $this->normalizeAnswer($userAnswer) === $this->normalizeAnswer($question->correct_answer);
                    if ($isCorrect) {
                        $score++;
                    }
                }

                ExamResponse::create([
                    'examinee_id' => $examinee->id,
                    'question_id' => $question->id,
                    'response' => $userAnswer ?? '',
                    'is_correct' => $isCorrect
                ]);
            }

            // Mark token as used
            Token::where('id', session('token_id'))->update([
                'is_used' => true,
                'used_at' => now(),
                'status' => Token::STATUS_USED
            ]);

            // Calculate proficiency level
            $totalQuestions = count($questions);
            $percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100) : 0;

            if ($percentage >= 95) {
                $proficiency = 'Passed';
            } elseif ($percentage >= 90) {
                $proficiency = 'Passed';
            } elseif ($percentage >= 85) {
                $proficiency = 'Passed';
            } elseif ($percentage >= 80) {
                $proficiency = 'Passed';
            } elseif ($percentage >= 76) {
                $proficiency = 'Passed';
            } elseif ($percentage == 75) {
                $proficiency = 'Passed';
            } else {
                $proficiency = 'Failed';
            }

            // Create exam result record
            ExamResult::create([
                'examinee_id' => $examinee->id,
                'exam_id' => $exam->id,
                'score' => $score,
                'total_questions' => $totalQuestions,
                'percentage' => $percentage,
                'rating' => $proficiency,
                'status' => 'completed'
            ]);

            // Store results in session FIRST
            session([
                'exam_score' => $score,
                'total_questions' => $totalQuestions,
                'proficiency' => $proficiency,
                'exam_submitted' => true,
                'examinee_full_name' => session('examinee_full_name')
            ]);

            // Clear other exam data from session
            session()->forget([
                'exam_started',
                'exam_start_time',
                'exam_end_time',
                'questions',
                'current_question',
                'answers'
            ]);

            \Log::info('Exam submitted successfully', [
                'examinee_id' => $examinee->id,
                'score' => $score,
                'total_questions' => $totalQuestions
            ]);

            \Log::debug('Session before redirect', session()->all());

            return redirect()->route('exam.results');

        } catch (\Exception $e) {
            \Log::error('Submit Exam Error: ' . $e->getMessage());
            \Log::debug('Redirecting to results', [
                'session_data' => session()->all(),
                'route' => route('exam.results')
            ]);
            return redirect()->route('exam.instructions')->with('error', 'An unexpected error occurred while submitting your exam. Please contact the administrator.');
        }
    }

    private function normalizeAnswer($text) {
        return strtolower(
            preg_replace('/\s+/', ' ',
                preg_replace('/[^a-zA-Z0-9\s]/', '', trim($text))
            )
        );
    }

    public function showCertification()
    {
        if (!session()->has('exam_started')) {
            return redirect()->route('exam.instructions')->with('error', 'Please start the exam first.');
        }

        // Check if time has actually expired
        $remainingTime = max(0, strtotime(session('exam_end_time')) - time());
        if ($remainingTime > 0) {
            return redirect()->route('exam.question', ['question_number' => session('current_question', 1)]);
        }

        return view('exam.certification');
    }

    public function showResults()
    {
        if (!session()->has('exam_submitted')) {
            \Log::error('Attempt to access results without submission', [
                'session_data' => session()->all()
            ]);
            return redirect()->route('exam.start')->with('error', 'Please complete an exam first to view results.');
        }
        
        // Verify we have all required result data
        if (!session()->has('exam_score') || !session()->has('total_questions') || !session()->has('proficiency')) {
            \Log::error('Incomplete results data in session', [
                'session_data' => session()->all()
            ]);
            return redirect()->route('exam.start')->with('error', 'Exam results data is incomplete. Please contact support.');
        }
        
        return view('exam.results', [
            'examinee' => session('examinee_full_name'),
            'score' => session('exam_score'),
            'total' => session('total_questions'),
            'proficiency' => session('proficiency')
        ]);
    }

    public function finishExam()
    {
        try {
            \Log::debug('Finishing exam', ['session' => session()->all()]);
            
            $message = session()->has('exam_score') 
                ? 'Exam session closed. Thank you!' 
                : 'Session cleared';
                
            session()->flush();
            
            return redirect()->route('welcome')->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Finish Exam Error: ' . $e->getMessage());
            return redirect()->route('welcome')->with('error', 'An error occurred while closing the session.');
        }
    }
    
    public function getSubunits(Request $request)
    {
        $unitId = $request->unitId;
        $subunit = Subunit::select('SubUnitId','Description')
                        ->where('UnitId',$unitId)
                        ->where('IsActive',1)
                        ->get();                                              
        return response()->json($subunit);          
    }

    public function getStations(Request $request)
    {
        $subunitId = $request->subunitId;
        $station = Station::select('StationId','Name')
                        ->where('SubUnitId',$subunitId)
                        ->where('IsActive',1)
                        ->get();                                              
        return response()->json($station);          
    }

    public function resumeExam()
    {
        try {
            $examinee = Examinee::where('token_id', session('token_id'))->first();
            
            if (!$examinee) {
                \Log::error('Resume Exam: No examinee found for token_id: ' . session('token_id'));
                return redirect()->route('exam.instructions')->with('error', 'Examinee record not found.');
            }

            if (!$examinee->last_question_number || empty($examinee->exam_progress)) {
                \Log::error('Resume Exam: No progress data found', [
                    'examinee_id' => $examinee->id,
                    'has_last_question' => !empty($examinee->last_question_number),
                    'has_exam_progress' => !empty($examinee->exam_progress)
                ]);
                return redirect()->route('exam.instructions')->with('error', 'No exam progress to resume.');
            }

            $exam = Exam::first();
            if (!$exam) {
                \Log::error('Resume Exam: No exam found');
                return redirect()->route('exam.instructions')->with('error', 'Exam not found.');
            }

            // Validate exam progress data
            if (!isset($examinee->exam_progress['questions']) || !is_array($examinee->exam_progress['questions'])) {
                \Log::error('Resume Exam: Invalid exam_progress format', ['exam_progress' => $examinee->exam_progress]);
                return redirect()->route('exam.instructions')->with('error', 'Invalid exam progress data.');
            }

            // Calculate remaining time (max duration or remaining time)
            $originalDuration = $exam->duration_minutes * 60;
            $elapsedTime = $examinee->updated_at->diffInSeconds(now());
            $remainingTime = max(0, $originalDuration - $elapsedTime);

            // Restore exam session
            session([
                'exam_started' => true,
                'exam_start_time' => now()->subSeconds($originalDuration - $remainingTime)->toDateTimeString(),
                'exam_end_time' => now()->addSeconds($remainingTime)->toDateTimeString(),
                'questions' => $examinee->exam_progress['questions'],
                'current_question' => $examinee->last_question_number - 1, // Convert to 0-based index
                'answers' => $examinee->exam_progress['answers'] ?? []
            ]);

            \Log::info('Exam resumed successfully', [
                'examinee_id' => $examinee->id,
                'last_question_number' => $examinee->last_question_number,
                'remaining_time' => $remainingTime
            ]);

            return redirect()->route('exam.question', [
                'question_number' => $examinee->last_question_number
            ]);

        } catch (\Exception $e) {
            \Log::error('Resume Exam Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('exam.instructions')->with('error', 'Failed to resume exam. Please start a new exam.');
        }
    }
}
