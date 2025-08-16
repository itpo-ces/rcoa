<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';
    
    protected $fillable = ['exam_id', 'question_text', 'type', 'difficulty', 'correct_answer', 'options', 'is_active'];
    
    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
    ];
    
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function responses()
    {
        return $this->hasMany(ExamResponse::class);
    }

    public static function getTypeValues(): array
    {
        return ['multiple_choice', 'fill_in_the_blanks', 'true_or_false', 'yes_or_no'];
    }

    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'multiple_choice'   => 'Multiple Choice',
            'fill_in_the_blanks'=> 'Fill in the Blanks',
            'true_or_false'     => 'True or False',
            'yes_or_no'         => 'Yes or No',
        ];

        return $labels[$this->type] ?? $this->type;
    }


    public static function getDifficultyValues(): array
    {
        return ['easy', 'moderate', 'difficult', 'extra_difficult'];
    }

    public function getDifficultyLabelAttribute(): string
    {
        $labels = [
            'easy'            => 'Easy',
            'moderate'       => 'Moderate',
            'difficult'      => 'Difficult',
            'extra_difficult'=> 'Extra Difficult',
        ];

        return $labels[$this->difficulty] ?? $this->difficulty;
    }

    public static function getEnumValues($column)
    {
        $type = \DB::select("SHOW COLUMNS FROM " . (new self)->getTable() . " WHERE Field = '{$column}'")[0]->Type;
    
        preg_match('/^enum\((.*)\)$/', $type, $matches);
    
        $enum = [];
        foreach (explode(',', $matches[1]) as $value) {
            $v = trim($value, "'");
            $enum[] = [
                'id' => $v,
                'description' => ucwords(strtoupper(str_replace('_', ' ', $v)))
            ];
        }
    
        return $enum;
    }
}
