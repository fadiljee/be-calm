<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal.
     * Harus sama dengan field yang dikirim dari Flutter.
     */
    protected $fillable = [
        'student_id',
        'content',
        'mood',
        'mood_label',
        'date',
    ];

    /**
     * Relasi ke User (Siswa)
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}