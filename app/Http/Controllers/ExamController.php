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
        return view('exam.start');
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

            // Retrieve validated data
            $validated = $validator->validated();

            $token = Token::where('token', $validated['token'])
                        ->where('is_used', false)
                        ->first();

            if (!$token) {
                return back()->with('error', 'Invalid or already used token.');
            }

            session(['valid_token' => true, 'token_id' => $token->id]);

            return redirect()->route('exam.privacy');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Token validation failed: ' . $e->getMessage());

            // Redirect back with a generic error message
            return back()->with('error', 'Something went wrong while validating the token. Please try again later.');
        }
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
        $units = Unit::where('IsActive', '=', '1')
                    ->where('isRegionalBased', 1)->get();

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
            'unit' => 'required|string',
            'subunit' => 'nullable|string',
            'station' => 'nullable|string',
        ]);

        try {
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
            // Validate that certification was accepted
            if (!$request->has('certified') || $request->certified !== '1') {
                return back()->with('error', 'You must certify the statement before starting the exam.');
            }
            $exam = Exam::first();

            if (!$exam) {
                return back()->with('error', 'Exam is not available. Please contact the administrator.');
            }

            // $questions = $exam->questions()->inRandomOrder()->get();

            $questionLimit = $exam->number_of_questions ?? 30;

            $questions = $exam->questions()
                    ->where('is_active', 1)
                    ->inRandomOrder()
                    ->limit($questionLimit)
                    ->get();

            if ($questions->isEmpty()) {
                return back()->with('error', 'No questions found for this exam.');
            }

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
            \Log::error('Start Exam Error: ' . $e->getMessage());

            return back()->with('error', 'An unexpected error occurred while starting the exam. Please try again.');
        }
    }
    
    public function showQuestion($question_number)
    {
        try {
            // Validate exam session
            if (!session()->has('exam_started')) {
                return redirect()->route('exam.instructions')->with('error', 'Please start the exam first.');
            }

            // Check if time has expired
            $remainingTime = max(0, strtotime(session('exam_end_time')) - time());
            if ($remainingTime <= 0) {
                return redirect()->route('exam.certification');
            }

            $question_index = $question_number - 1;
            $questions = session('questions');

            if (!is_array($questions) || $question_index < 0 || $question_index >= count($questions)) {
                abort(404);
            }

            $question = Question::find($questions[$question_index]);

            if (!$question) {
                return redirect()->route('exam.instructions')->with('error', 'Question not found.');
            }

            // Calculate remaining time in seconds
            $remainingTime = max(0, strtotime(session('exam_end_time')) - time());

            if ($remainingTime <= 0) {
                // return redirect()->route('exam.submit')->with('warning', 'Time has expired.');
                return redirect()->route('exam.certification');
            }

            return view('exam.question', [
                'question' => $question,
                'question_number' => $question_number,
                'total_questions' => count($questions),
                'remaining_time' => $remainingTime
            ]);
        } catch (\Exception $e) {
            \Log::error('Show Question Error: ' . $e->getMessage());

            return redirect()->route('exam.instructions')->with('error', 'An error occurred while loading the question. Please try again.');
        }
    }
    
    public function saveAnswer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'question_id' => 'required|exists:questions,id',
                'answer' => 'required'
            ], [
                'question_id.required' => 'Question ID is required.',
                'question_id.exists' => 'The selected question does not exist.',
                'answer.required' => 'Please select an answer before continuing.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->all()
                ], 422); // Unprocessable Entity
            }

            // Save the answer in session
            $answers = session('answers', []);
            $answers[$request->question_id] = $request->answer;
            session(['answers' => $answers]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Save Answer Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'errors' => ['An unexpected error occurred while saving the answer. Please try again.']
            ], 500); // Internal Server Error
        }
    }

    public function submitExam(Request $request)
    {
        try {
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
                'accepted_certification' => true
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
                'used_at' => now()
            ]);

            // // Calculate proficiency level
            // $totalQuestions = count($questions);
            // $percentage = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;

            // if ($percentage >= 90) {
            //     $proficiency = 'Expert';
            // } elseif ($percentage >= 75) {
            //     $proficiency = 'Proficient';
            // } elseif ($percentage >= 50) {
            //     $proficiency = 'Moderate';
            // } else {
            //     $proficiency = 'Needs Improvement';
            // }

            // Calculate proficiency level
            $totalQuestions = count($questions);
            $percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100) : 0;

            // if ($percentage >= 95) {
            //     $proficiency = 'Passed (100%)';
            // } elseif ($percentage >= 90) {
            //     $proficiency = 'Passed (94%)';
            // } elseif ($percentage >= 85) {
            //     $proficiency = 'Passed (89%)';
            // } elseif ($percentage >= 80) {
            //     $proficiency = 'Passed (84%)';
            // } elseif ($percentage >= 76) {
            //     $proficiency = 'Passed (80%)';
            // } elseif ($percentage == 75) {
            //     $proficiency = 'Passed (75%)';
            // } else {
            //     $proficiency = 'Failed (' . $percentage . '%)';
            // }

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

            // Store results in session
            session([
                'exam_score' => $score,
                'total_questions' => $totalQuestions,
                'proficiency' => $proficiency
            ]);

            return redirect()->route('exam.results');

        } catch (\Exception $e) {
            \Log::error('Submit Exam Error: ' . $e->getMessage());
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
        if (!session()->has('exam_score')) {
            return redirect()->route('exam.start');
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
        session()->flush();
        return redirect()->route('welcome')->with('success', 'Exam session closed. Thank you!');
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
}
