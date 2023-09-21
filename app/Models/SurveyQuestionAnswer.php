<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestionAnswer extends Model
{
    const CREATED_AT  = null;
    const UPDATED_AT = null;
    use HasFactory;
    protected $fillable = ['survey_question_id', 'survey_answer_id', 'answer']; 
}
