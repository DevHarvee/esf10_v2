<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'fname', 'mname', 'lname', 'ext', 'bday', 'sex', 'sname',
        'grade', 'section', 'uid', 'grading', 'sy', 'lrn', 'final_rating', 'status',
    ];

    public function adviser()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
}
