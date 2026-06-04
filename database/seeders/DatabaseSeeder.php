<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin (Guru BK)
        User::create([
            'name'           => 'Riskie Relica, S.Pd',
            'email'          => 'admin@calmspace.com',
            'password'       => Hash::make('password'),
            'role'           => 'admin',
            'phone'          => '6282234567891',
            'specialization' => 'Konseling Akademik & Karier',
            'bio'            => 'Siap membantu menemukan potensi terbaikmu 🌿',
        ]);

        // 2. Data Siswa
        $students = [
            ['AIGA BUNGA SAYVIANI', '0096196341', 'XI-2'],
            ['DINDA ARISKA', '0099230190', 'XI-4'],
            ['DESTI PRATIWI', '0084190309', 'XI-1'],
            ['MARSELA', '0081485468', 'XI-4'],
            ['AURA MIRANDA ANINDITA', '3097954277', 'XI-2'],
            ['JIHAN AURELLYA LARASATI', '0094572439', 'XI-2'],
            ['BUNGA HARUM PURNAMA', '0091256569', 'XI-2'],
            ['KEYRIN FETRIANI SYAHRAM', '0082838802', 'XI-3'],
            ['DIAH NUR KARIMAH', '0093897836', 'XI-4'],
            ['SARAH AFIFAH', '0091720232', 'XI-1'],
            ['ADITIA PERATAMA', '0096843665', 'XI-3'],
            ['GHAZIAN DHANIS ALZAHY', '0097214822', 'XI-2'],
            ['MARCEL PRADANA', '0095330050', 'XI-4'],
            ['SALU ARDIAN', '0087460675', 'XI-2'],
            ['RISKI FADILA', '0098075101', 'XI-3'],
            ['MAULANA SAKA IBRAHIM', '0092672867', 'XI-4'],
            ['MUHAMMAD FACHREZI YAHYA', '0094419983', 'XI-3'],
            ['RAJA BOY', '0091456905', 'XI-3'],
            ['VIAN', '0084776286', 'XI-3'],
            ['MUHAMMAD DIPA', '0099007039', 'XI-3'],
        ];

        foreach ($students as $s) {
            User::create([
                'name'     => $s[0],
                'nisn'     => $s[1],
                'kelas'    => $s[2],
                'role'     => 'student',
                'password' => Hash::make('password123'),
            ]);
        }

        // 3. 30 Pertanyaan Asesmen
        $questions = [
            // A. KECEMASAN
            ['Saya merasa cemas tanpa alasan yang jelas', 'kecemasan'],
            ['Saya sering merasa gelisah atau tidak tenang', 'kecemasan'],
            ['Saya mudah overthinking terhadap hal kecil', 'kecemasan'],
            ['Saya merasa takut akan sesuatu yang belum terjadi', 'kecemasan'],
            ['Saya sulit mengontrol pikiran negatif', 'kecemasan'],
            ['Saya merasa jantung berdebar saat memikirkan sesuatu', 'kecemasan'],
            ['Saya merasa sulit untuk rileks', 'kecemasan'],
            // B. STRES AKADEMIK
            ['Saya merasa terbebani dengan tugas sekolah', 'stres_akademik'],
            ['Saya kesulitan memahami pelajaran', 'stres_akademik'],
            ['Saya merasa tertekan dengan nilai akademik', 'stres_akademik'],
            ['Saya sering menunda pekerjaan sekolah', 'stres_akademik'],
            ['Saya merasa tidak mampu memenuhi harapan akademik', 'stres_akademik'],
            ['Saya merasa kelelahan karena kegiatan sekolah', 'stres_akademik'],
            ['Saya merasa takut gagal dalam pelajaran', 'stres_akademik'],
            // C. SOSIAL & LINGKUNGAN
            ['Saya merasa sulit bergaul dengan teman', 'sosial_lingkungan'],
            ['Saya merasa tidak diterima di lingkungan sekitar', 'sosial_lingkungan'],
            ['Saya sering merasa sendirian meskipun berada di antara teman', 'sosial_lingkungan'],
            ['Saya pernah merasa dijauhi atau dikucilkan', 'sosial_lingkungan'],
            ['Lingkungan sekitar membuat saya tidak nyaman', 'sosial_lingkungan'],
            ['Saya merasa terpengaruh oleh tekanan dari teman', 'sosial_lingkungan'],
            ['Saya sering membandingkan diri dengan orang lain', 'sosial_lingkungan'],
            ['Saya merasa harus terlihat lebih baik dari orang lain', 'sosial_lingkungan'],
            // D. KELUARGA & LATAR BELAKANG
            ['Saya merasa kurang mendapatkan perhatian dari orang tua', 'keluarga'],
            ['Saya sering mengalami konflik dalam keluarga', 'keluarga'],
            ['Saya merasa kondisi keluarga mempengaruhi perasaan saya', 'keluarga'],
            ['Saya merasa orang tua kurang memahami saya', 'keluarga'],
            ['Saya merasa tekanan dari keluarga membuat saya tidak nyaman', 'keluarga'],
            ['Saya merasa kondisi ekonomi keluarga mempengaruhi kehidupan saya', 'keluarga'],
            ['Saya merasa kurang mendapatkan dukungan dari keluarga', 'keluarga'],
            ['Saya merasa suasana rumah tidak nyaman bagi saya', 'keluarga'],
        ];

        foreach ($questions as $q) {
            Question::create([
                'question_text' => $q[0],
                'category'      => $q[1],
                'is_active'     => true,
            ]);
        }
    }
}