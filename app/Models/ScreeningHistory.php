<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreeningHistory extends Model
{
    use HasFactory;

   protected $fillable = ['student_id', 'total_score', 'grand_mean', 'conclusion'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(ScreeningAnswer::class, 'screening_history_id');
    }
}

