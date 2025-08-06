<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Support\Str;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $exam = Exam::first();
        
        // Multiple Choice Questions (30 questions)
        $mcQuestions = [
            [
                'question_text' => "What does 10-4 mean in radio communication?",
                'correct_answer' => "Message received/acknowledged",
                'options' => ["Emergency situation", "Message received/acknowledged", "Need assistance", "Stand by"]
            ],
            [
                'question_text' => "Which 10-code means 'Repeat your message'?",
                'correct_answer' => "10-9",
                'options' => ["10-1", "10-4", "10-9", "10-20"]
            ],
            [
                'question_text' => "What is the proper way to initiate a radio transmission?",
                'correct_answer' => "Say the recipient's call sign first, then your call sign",
                'options' => [
                    "Say your call sign first, then the recipient's call sign",
                    "Say the recipient's call sign first, then your call sign",
                    "Just start speaking immediately",
                    "Say 'Attention' followed by your message"
                ]
            ],
            [
                'question_text' => "What does 10-20 mean in radio communication?",
                'correct_answer' => "Location/position",
                'options' => ["Emergency", "Location/position", "Message received", "Out of service"]
            ],
            [
                'question_text' => "Which of these is proper radio etiquette?",
                'correct_answer' => "Speaking clearly and at a moderate pace",
                'options' => [
                    "Speaking as quickly as possible",
                    "Using slang to save time",
                    "Speaking clearly and at a moderate pace",
                    "Whispering to avoid others hearing"
                ]
            ],
            [
                'question_text' => "What does 'Roger that' mean in radio communication?",
                'correct_answer' => "Message received and understood",
                'options' => [
                    "I disagree",
                    "Message received and understood",
                    "Repeat your message",
                    "I need help"
                ]
            ],
            [
                'question_text' => "Which 10-code indicates an emergency?",
                'correct_answer' => "10-33",
                'options' => ["10-4", "10-7", "10-20", "10-33"]
            ],
            [
                'question_text' => "What is the purpose of using phonetic alphabet in radio communication?",
                'correct_answer' => "To ensure letters are clearly understood",
                'options' => [
                    "To make communication faster",
                    "To confuse eavesdroppers",
                    "To ensure letters are clearly understood",
                    "To sound more professional"
                ]
            ],
            [
                'question_text' => "What does 'Break, break' mean in radio communication?",
                'correct_answer' => "Interrupting to indicate urgent traffic",
                'options' => [
                    "The radio is broken",
                    "End of transmission",
                    "Interrupting to indicate urgent traffic",
                    "Request to switch channels"
                ]
            ],
            [
                'question_text' => "Which 10-code means 'Return to your station'?",
                'correct_answer' => "10-19",
                'options' => ["10-7", "10-19", "10-22", "10-99"]
            ],
            [
                'question_text' => "What is the phonetic word for the letter 'B'?",
                'correct_answer' => "Bravo",
                'options' => ["Baker", "Bravo", "Boston", "Beta"]
            ],
            [
                'question_text' => "What does 10-7 mean?",
                'correct_answer' => "Out of service",
                'options' => ["Out of service", "On duty", "Message received", "Need backup"]
            ],
            [
                'question_text' => "When should you use 'Over' in radio communication?",
                'correct_answer' => "When you're done speaking and awaiting a response",
                'options' => [
                    "When you want to change channels",
                    "When you're done speaking and awaiting a response",
                    "When you don't understand the message",
                    "When you want to interrupt"
                ]
            ],
            [
                'question_text' => "What does 10-6 mean?",
                'correct_answer' => "Busy, stand by",
                'options' => ["Busy, stand by", "Urgent", "Call your home", "Negative"]
            ],
            [
                'question_text' => "Which of these is NOT a proper radio procedure?",
                'correct_answer' => "Holding the microphone close to your mouth while breathing heavily",
                'options' => [
                    "Identifying yourself at the beginning and end of transmission",
                    "Holding the microphone close to your mouth while breathing heavily",
                    "Pausing briefly before speaking to avoid cutting off",
                    "Using clear, concise language"
                ]
            ],
            [
                'question_text' => "What does 'Say again' mean in radio communication?",
                'correct_answer' => "Repeat your last transmission",
                'options' => [
                    "I agree",
                    "Repeat your last transmission",
                    "Message received",
                    "Switch to another channel"
                ]
            ],
            [
                'question_text' => "Which 10-code means 'Affirmative/Yes'?",
                'correct_answer' => "10-2",
                'options' => ["10-1", "10-2", "10-3", "10-4"]
            ],
            [
                'question_text' => "What is the purpose of the word 'Copy' in radio communication?",
                'correct_answer' => "To indicate you received and understood the message",
                'options' => [
                    "To request a duplicate radio",
                    "To indicate you received and understood the message",
                    "To ask someone to write down the message",
                    "To confirm you're recording the transmission"
                ]
            ],
            [
                'question_text' => "What does 10-99 mean?",
                'correct_answer' => "Emergency, all units respond",
                'options' => [
                    "Mission completed",
                    "Emergency, all units respond",
                    "Need medical assistance",
                    "Return to base"
                ]
            ],
            [
                'question_text' => "Which phonetic word represents the letter 'M'?",
                'correct_answer' => "Mike",
                'options' => ["Mary", "Mike", "Mama", "Moon"]
            ],
            [
                'question_text' => "What does 'Stand by' mean in radio communication?",
                'correct_answer' => "Wait and I will respond shortly",
                'options' => [
                    "Switch to standby power",
                    "Wait and I will respond shortly",
                    "I don't understand",
                    "End of transmission"
                ]
            ],
            [
                'question_text' => "Which 10-code means 'Negative/No'?",
                'correct_answer' => "10-3",
                'options' => ["10-1", "10-2", "10-3", "10-4"]
            ],
            [
                'question_text' => "What is the correct way to say '123' in radio communication?",
                'correct_answer' => "One-two-three",
                'options' => [
                    "One hundred twenty-three",
                    "One-two-three",
                    "One twenty-three",
                    "Single two three"
                ]
            ],
            [
                'question_text' => "What does 10-22 mean?",
                'correct_answer' => "Disregard the last message",
                'options' => [
                    "Disregard the last message",
                    "Need police assistance",
                    "Vehicle accident",
                    "Person down"
                ]
            ],
            [
                'question_text' => "When should you use 'Out' in radio communication?",
                'correct_answer' => "When ending the conversation with no response expected",
                'options' => [
                    "When you're outside the coverage area",
                    "When ending the conversation with no response expected",
                    "When you want to go off the radio",
                    "When you can't hear the other party"
                ]
            ],
            [
                'question_text' => "What does 'Wilco' mean in radio communication?",
                'correct_answer' => "Will comply",
                'options' => [
                    "Will comply",
                    "Weak signal",
                    "Window communication",
                    "Wilderness coordinates"
                ]
            ],
            [
                'question_text' => "Which 10-code means 'In service'?",
                'correct_answer' => "10-8",
                'options' => ["10-1", "10-7", "10-8", "10-9"]
            ],
            [
                'question_text' => "What is the phonetic word for the number '5'?",
                'correct_answer' => "Fife",
                'options' => ["Five", "Fife", "Foxtrot", "Fire"]
            ],
            [
                'question_text' => "What does 10-13 mean?",
                'correct_answer' => "Advise weather and road conditions",
                'options' => [
                    "Advise weather and road conditions",
                    "Officer needs help",
                    "Prisoner in custody",
                    "Report to station"
                ]
            ],
            [
                'question_text' => "What is the purpose of using 'CQ' in amateur radio?",
                'correct_answer' => "General call to any station",
                'options' => [
                    "Emergency call",
                    "General call to any station",
                    "Call to specific station",
                    "End of transmission"
                ]
            ]
        ];

        // True/False Questions (10 questions)
        $tfQuestions = [
            [
                'question_text' => "10-1 means 'Receiving poorly' in radio communication.",
                'correct_answer' => "True"
            ],
            [
                'question_text' => "It's acceptable to use slang and abbreviations in official radio communications.",
                'correct_answer' => "False"
            ],
            [
                'question_text' => "The phonetic word for 'Z' is 'Zulu'.",
                'correct_answer' => "True"
            ],
            [
                'question_text' => "10-32 means 'Man with gun' in police radio codes.",
                'correct_answer' => "True"
            ],
            [
                'question_text' => "You should hold the microphone directly against your mouth for clearest transmission.",
                'correct_answer' => "False"
            ],
            [
                'question_text' => "The term 'Mayday' is used in aviation and maritime radio communications to signal distress.",
                'correct_answer' => "True"
            ],
            [
                'question_text' => "10-50 means 'Vehicle accident' in many police codes.",
                'correct_answer' => "True"
            ],
            [
                'question_text' => "It's good practice to transmit long, detailed messages without pausing.",
                'correct_answer' => "False"
            ],
            [
                'question_text' => "The phonetic word for '9' is 'Niner'.",
                'correct_answer' => "True"
            ],
            [
                'question_text' => "10-42 means 'Ending tour of duty' in police codes.",
                'correct_answer' => "True"
            ]
        ];

        // Fill in the Blanks Questions (10 questions)
        $fitbQuestions = [
            [
                'question_text' => "The phonetic word for the letter 'C' is ______.",
                'correct_answer' => "Charlie"
            ],
            [
                'question_text' => "The 10-code ______ means 'Call your home'.",
                'correct_answer' => "10-17"
            ],
            [
                'question_text' => "When you say '______', it means you've received and understood the message.",
                'correct_answer' => "Copy"
            ],
            [
                'question_text' => "The complete phrase to end a conversation with no response expected is '______'.",
                'correct_answer' => "Out"
            ],
            [
                'question_text' => "10-______ means 'Urgent' in radio communication.",
                'correct_answer' => "12"
            ],
            [
                'question_text' => "The phonetic word for the number '3' is ______.",
                'correct_answer' => "Tree"
            ],
            [
                'question_text' => "The international radio distress signal is '______' repeated three times.",
                'correct_answer' => "Mayday"
            ],
            [
                'question_text' => "10-______ means 'Report in person' in many police codes.",
                'correct_answer' => "15"
            ],
            [
                'question_text' => "The proper way to say 'A123' in radio communication is '______'.",
                'correct_answer' => "Alpha-one-two-three"
            ],
            [
                'question_text' => "The 10-code ______ means 'Pick up prisoner' in police communications.",
                'correct_answer' => "10-23"
            ]
        ];

        // Create Multiple Choice Questions
        foreach ($mcQuestions as $mcQuestion) {
            Question::create([
                'exam_id' => $exam->id,
                'question_text' => $mcQuestion['question_text'],
                'type' => 'multiple_choice',
                'difficulty' => $this->randomDifficulty(),
                'correct_answer' => $mcQuestion['correct_answer'],
                'options' => json_encode($mcQuestion['options'])
            ]);
        }

        // Create True/False Questions
        foreach ($tfQuestions as $tfQuestion) {
            Question::create([
                'exam_id' => $exam->id,
                'question_text' => $tfQuestion['question_text'],
                'type' => 'true_or_false',
                'difficulty' => $this->randomDifficulty(),
                'correct_answer' => $tfQuestion['correct_answer']
            ]);
        }

        // Create Fill in the Blanks Questions
        foreach ($fitbQuestions as $fitbQuestion) {
            Question::create([
                'exam_id' => $exam->id,
                'question_text' => $fitbQuestion['question_text'],
                'type' => 'fill_in_the_blanks',
                'difficulty' => $this->randomDifficulty(),
                'correct_answer' => $fitbQuestion['correct_answer']
            ]);
        }
    }

    private function randomDifficulty()
    {
        $difficulties = ['easy', 'moderate', 'hard', 'difficult'];
        return $difficulties[array_rand($difficulties)];
    }
}