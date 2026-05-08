<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'activity_date',
        'title',
        'description',
        'mood',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
