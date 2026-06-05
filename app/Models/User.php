<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// TAMBAHKAN kolom phone, specialization, dan bio di sini
#[Fillable([
    'name', 
    'email', 
    'password', 
    'role', 
    'nisn', 
    'kelas',
    'parent_id',
    'phone',          // Untuk nomor WhatsApp Guru BK
    'specialization', // Untuk bidang keahlian Guru BK
    'bio'             // Untuk tagline/profil singkat
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    // Relasi untuk Parent & Student
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    // Relasi Fitur-Fitur CalmSpace
    // public function screeningHistories()
    // {
    //     return $this->hasMany(ScreeningHistory::class, 'student_id');
    // }
    // Tambahkan ini di dalam class User
    public function sentMessages()
    {
        // Sesuaikan 'Message' dengan nama model tabel chat kamu
        return $this->hasMany(\App\Models\Message::class, 'sender_id'); 
    }
    public function dailyActivities()
    {
        return $this->hasMany(DailyActivity::class, 'student_id');
    }

    // Relasi untuk Jurnal Harian (Tambahkan ini jika belum ada)
    public function journals()
    {
        return $this->hasMany(Journal::class, 'student_id');
    }

    // Relasi Konseling
    public function studentConsultations()
    {
        return $this->hasMany(Consultation::class, 'student_id');
    }

    public function adminConsultations()
    {
        return $this->hasMany(Consultation::class, 'admin_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // app/Models/User.php

    public function screeningHistories() // Nama fungsi ini sangat penting
    {
        return $this->hasMany(ScreeningHistory::class, 'student_id');
    }
}