<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    public function questions()
    {
        return $this->belongsToMany('App\Models\Question', 'form_to_questions', 'form_id', 'question_id');
    }
}