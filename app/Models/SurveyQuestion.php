<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'data', 'description', 'survey_id', 'type'];
    public function survey(){
        return $this->belongsTo(Survey::class);
    }
}
