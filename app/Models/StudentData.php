<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class StudentData extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // Tentukan nama tabel agar Laravel tidak mencari tabel 'student_data's
    protected $table = 'student_data';

    // Sesuaikan kolom dengan struktur tabel baru Anda
    protected $fillable = [
        'name',
        'nisn',
        'password',
        'kelas',
    ];

    // Sembunyikan password agar tidak bocor di API response
    protected $hidden = [
        'password',
    ];

    // Jika Anda tidak menggunakan kolom 'updated_at', 
    // tambahkan baris di bawah ini:
    // public $timestamps = false;
}