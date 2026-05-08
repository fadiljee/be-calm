<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin (Guru BK) dengan Data Konseling Lengkap
        User::create([
            'name'           => 'Riskie Relica, S.Pd',
            'email'          => 'admin@calmspace.com',
            'password'       => Hash::make('password'),
            'role'           => 'admin',
            'phone'          => '6282234567891', // Format 62 agar langsung chat WA
            'specialization' => 'Konseling Akademik & Karier',
            'bio'            => 'Siap membantu menemukan potensi terbaikmu 🌿',
        ]);

        User::create([
            'name'           => 'Sonia Latifa, S.Pd',
            'email'          => 'sonia@calmspace.com',
            'password'       => Hash::make('password'),
            'role'           => 'admin',
            'phone'          => '6281298765432',
            'specialization' => 'Konseling Pribadi & Sosial',
            'bio'            => 'Ruang aman untuk cerita dan tumbuh bersama 🌺',
        ]);

        // 2. Parent
        $parent = User::create([
            'name'     => 'Bapak Siswa',
            'email'    => 'parent@calmspace.com',
            'password' => Hash::make('password'),
            'role'     => 'parent',
        ]);

        // 3. Student (Siswa)
        User::create([
            'name'      => 'Siswa Teladan',
            'email'     => 'student@calmspace.com',
            'password'  => Hash::make('password'),
            'role'      => 'student',
            'nisn'      => '12345678',
            'parent_id' => $parent->id,
        ]);

        // 4. Initial Questions
        $questions = [
            ['text' => 'Apakah Anda merasa sering cemas?', 'cat' => 'anxiety'],
            ['text' => 'Apakah Anda merasa sedih berkepanjangan?', 'cat' => 'depression'],
            ['text' => 'Apakah Anda merasa tertekan dengan tugas sekolah?', 'cat' => 'stress'],
            ['text' => 'Apakah Anda sulit tidur nyenyak?', 'cat' => 'stress'],
            ['text' => 'Apakah Anda merasa kehilangan minat pada hobi?', 'cat' => 'depression'],
        ];

        foreach ($questions as $q) {
            Question::create([
                'question_text' => $q['text'],
                'category'      => $q['cat'],
                'is_active'     => true,
            ]);
        }
    }
}