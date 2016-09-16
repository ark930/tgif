<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function forms()
    {
        return $this->belongsToMany('App\Models\Form', 'form_to_questions', 'form_id', 'question_id');
    }
}