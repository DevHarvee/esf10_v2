<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grading extends Model
{
    protected $table = 'grading';

    protected $fillable = ['grading', 'sy'];
}
