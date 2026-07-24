<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputtedGrade extends Model
{
    protected $table = 'inputted_grades';

    protected $fillable = [
        'sname_id', 'uid', 'section', 'grade', 'grading', 'sy',
        's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 's9', 's10', 's11', 's12',
    ];
}
