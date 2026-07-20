<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreeningAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['screening_history_id', 'question_id', 'score'];

    public function screeningHistory()
    {
        return $this->belongsTo(ScreeningHistory::class, 'screening_history_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
