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

class ExamControllerCopy extends Controller
{
    public function start()
    {
        return view('exam.start');
    }
    
    public function validateToken(Request $request)
    {
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
    }
    
    public function privacy()
    {
        return view('exam.privacy');
    }
    
    public function acceptPrivacy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accept' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        Examinee::updateOrCreate(
            ['token_id' => session('token_id')],
            [
                'accepted_privacy' => true,
                'rank' => 'Temporary', // Default values for required fields
                'first_name' => 'Temporary',
                'last_name' => 'Temporary',
                'designation' => 'Temporary',
                'unit' => 'Temporary',
                'subunit' => 'Temporary'
            ]
        );
        
        return redirect()->route('exam.register');
    }
    
    public function register()
    {
        $units = Unit::where('IsActive', '=', '1')->get();
        return view('exam.register', compact('units'));
    }
    
    public function storeRegistration(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'rank' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'qualifier' => 'nullable|string',
            'designation' => 'required|string',
            'unit' => 'required|string',
            'subunit' => 'required|string',
            'station' => 'nullable|string',
        ]);
        
        Examinee::updateOrCreate(
            ['token_id' => session('token_id')],
            $validated
        );

        // get the examinee's information
        $examinee = Examinee::where('token_id', session('token_id'))->first();

        if (!$examinee) {
            return redirect()->back()->with('error', 'Examinee registration failed. Please try again.');
        }

        // Get the Full Name of the Examinee Rank, First Name, Middle Name, Last Name, and Qualifier
        $fullName = strtoupper(trim("{$examinee->rank} {$examinee->first_name} {$examinee->middle_name} {$examinee->last_name} {$examinee->qualifier}"));

        // Store examinee full name in session
        session([
            'examinee_full_name' => $fullName
        ]);

        return redirect()->route('exam.instructions');
    }
    
    public function instructions()
    {
        return view('exam.instructions');
    }
    
    public function startExam(Request $request)
    {
        $exam = Exam::first();
        $questions = $exam->questions()->inRandomOrder()->get();
        
        session([
            'exam_started' => true,
            'exam_start_time' => now()->toDateTimeString(), // Explicitly convert to string
            'exam_end_time' => now()->addMinutes($exam->duration_minutes)->toDateTimeString(),
            'questions' => $questions->pluck('id')->toArray(),
            'current_question' => 0,
            'answers' => []
        ]);
        
        return redirect()->route('exam.question', ['question_number' => 1]);
    }
    
    public function showQuestion($question_number)
    {
        // Validate exam session first
        if (!session()->has('exam_started')) {
            return redirect()->route('exam.instructions')->with('error', 'Please start the exam first.');
        }

        $question_index = $question_number - 1;
        $questions = session('questions');
        
        if ($question_index < 0 || $question_index >= count($questions)) {
            abort(404);
        }
        
        $question = Question::find($questions[$question_index]);
        
        // Calculate remaining time in seconds
        $remainingTime = max(0, strtotime(session('exam_end_time')) - time());
        
        // If time is already up, redirect to submission
        if ($remainingTime <= 0) {
            return redirect()->route('exam.submit')->with('warning', 'Time has expired.');
        }

        return view('exam.question', [
            'question' => $question,
            'question_number' => $question_number,
            'total_questions' => count($questions),
            'remaining_time' => $remainingTime
        ]);
    }
    
    public function saveAnswer(Request $request)
    {
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
            ], 422); // 422 is Unprocessable Entity
        }

        // Save the answer in session
        $answers = session('answers', []);
        $answers[$request->question_id] = $request->answer;
        session(['answers' => $answers]);
        
        return response()->json(['success' => true]);
    }
    
    public function submitExam(Request $request)
    {
        $request->validate([
            'certify' => 'required|accepted'
        ]);
        
        $exam = Exam::first();
        $questions = Question::whereIn('id', session('questions'))->get();
        $answers = session('answers', []);
        $examinee = Examinee::where('token_id', session('token_id'))->first();
        
        $score = 0;
        
        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect = false;
            
            if ($userAnswer) {
                switch ($question->type) {
                    case 'multiple_choice':
                    case 'true_or_false':
                        $isCorrect = strtolower(trim($userAnswer)) === strtolower(trim($question->correct_answer));
                        break;
                    case 'fill_in_the_blanks':
                        // More flexible comparison for fill-in-the-blank
                        $isCorrect = strtolower(trim($userAnswer)) === strtolower(trim($question->correct_answer));
                        break;
                }
                
                if ($isCorrect) {
                    $score++;
                }
            }
            
            ExamResponse::create([
                'examinee_id' => $examinee->id,
                'question_id' => $question->id,
                'response' => $userAnswer,
                'is_correct' => $isCorrect
            ]);
        }
        
        // Mark token as used
        Token::where('id', session('token_id'))->update([
            'is_used' => true,
            'used_at' => now()
        ]);
        
        // Calculate proficiency level
        $percentage = ($score / count($questions)) * 100;
        
        if ($percentage >= 90) {
            $proficiency = 'Expert';
        } elseif ($percentage >= 75) {
            $proficiency = 'Proficient';
        } elseif ($percentage >= 50) {
            $proficiency = 'Moderate';
        } else {
            $proficiency = 'Needs Improvement';
        }
        
        session([
            'exam_score' => $score,
            'total_questions' => count($questions),
            'proficiency' => $proficiency
        ]);
        
        return redirect()->route('exam.results');
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

    
    public function getSubunits(Request $request){
        $unitId = $request->unitId;
        $subunit = Subunit::select('SubUnitId','Description')
                        ->where('UnitId',$unitId)
                        ->where('IsActive',1)
                        ->get();                                              
        return response()->json($subunit);          
    }

    public function getStations(Request $request){
        $subunitId = $request->subunitId;
        $station = Station::select('StationId','Name')
                        ->where('SubUnitId',$subunitId)
                        ->where('IsActive',1)
                        ->get();                                              
        return response()->json($station);          
    }
}
