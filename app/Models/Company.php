<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    public function employees()
    {
        return $this->hasMany('App\Models\Employee');
    }

    public function ceo()
    {
        return $this->belongsTo('App\Models\Employee', 'ceo_id');
    }

    public function nowForm()
    {
        return $this->belongsTo('App\Models\Form', 'form_id');
    }
}