<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'uid';

    protected $fillable = [
        'fullname',
        'email',
        'pwd',
        'utype',
        'assigned_grade',
        'assigned_section',
    ];

    protected $hidden = [
        'pwd',
    ];

    public function getAuthPassword(): string
    {
        return $this->pwd;
    }

    public function isAdmin(): bool
    {
        return $this->utype === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->utype === 'teacher';
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'uid', 'uid');
    }
}
