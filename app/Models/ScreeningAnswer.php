<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreeningAnswer extends Model
{
    protected $guarded = [];

    // Relasi untuk mengambil teks pertanyaan
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}