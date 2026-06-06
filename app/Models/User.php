<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nisn',
        'kelas',
        'parent_id',
        'phone',
        'specialization',
        'bio',
        'journal_pin',
        'otp_code',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'journal_pin',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at'    => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relasi Keluarga ────────────────────────────────────────────────────

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    // ─── Relasi Fitur CalmSpace ──────────────────────────────────────────────

    public function screeningHistories()
    {
        return $this->hasMany(ScreeningHistory::class, 'student_id');
    }

    public function journals()
    {
        return $this->hasMany(Journal::class, 'student_id');
    }

    public function dailyActivities()
    {
        return $this->hasMany(DailyActivity::class, 'student_id');
    }

    public function studentConsultations()
    {
        return $this->hasMany(Consultation::class, 'student_id');
    }

    public function adminConsultations()
    {
        return $this->hasMany(Consultation::class, 'admin_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
}