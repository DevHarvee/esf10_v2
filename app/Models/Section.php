<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sections';

    protected $fillable = ['grade_yr', 'section'];
}
