<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingTerm extends Model
{
    protected $table = 'grading_terms';

    protected $fillable = ['term_name', 'term_order'];
}
