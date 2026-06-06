<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            // A. Kecemasan
            ['Saya merasa cemas tanpa alasan yang jelas',                   'kecemasan'],
            ['Saya sering merasa gelisah atau tidak tenang',                'kecemasan'],
            ['Saya mudah overthinking terhadap hal kecil',                  'kecemasan'],
            ['Saya merasa takut akan sesuatu yang belum terjadi',           'kecemasan'],
            ['Saya sulit mengontrol pikiran negatif',                       'kecemasan'],
            ['Saya merasa jantung berdebar saat memikirkan sesuatu',        'kecemasan'],
            ['Saya merasa sulit untuk rileks',                              'kecemasan'],

            // B. Stres Akademik
            ['Saya merasa terbebani dengan tugas sekolah',                  'stres_akademik'],
            ['Saya kesulitan memahami pelajaran',                           'stres_akademik'],
            ['Saya merasa tertekan dengan nilai akademik',                  'stres_akademik'],
            ['Saya sering menunda pekerjaan sekolah',                       'stres_akademik'],
            ['Saya merasa tidak mampu memenuhi harapan akademik',           'stres_akademik'],
            ['Saya merasa kelelahan karena kegiatan sekolah',               'stres_akademik'],
            ['Saya merasa takut gagal dalam pelajaran',                     'stres_akademik'],

            // C. Sosial & Lingkungan
            ['Saya merasa sulit bergaul dengan teman',                      'sosial_lingkungan'],
            ['Saya merasa tidak diterima di lingkungan sekitar',            'sosial_lingkungan'],
            ['Saya sering merasa sendirian meskipun berada di antara teman','sosial_lingkungan'],
            ['Saya pernah merasa dijauhi atau dikucilkan',                  'sosial_lingkungan'],
            ['Lingkungan sekitar membuat saya tidak nyaman',                'sosial_lingkungan'],
            ['Saya merasa terpengaruh oleh tekanan dari teman',             'sosial_lingkungan'],
            ['Saya sering membandingkan diri dengan orang lain',            'sosial_lingkungan'],
            ['Saya merasa harus terlihat lebih baik dari orang lain',       'sosial_lingkungan'],

            // D. Keluarga & Latar Belakang
            ['Saya merasa kurang mendapatkan perhatian dari orang tua',     'keluarga'],
            ['Saya sering mengalami konflik dalam keluarga',                'keluarga'],
            ['Saya merasa kondisi keluarga mempengaruhi perasaan saya',     'keluarga'],
            ['Saya merasa orang tua kurang memahami saya',                  'keluarga'],
            ['Saya merasa tekanan dari keluarga membuat saya tidak nyaman', 'keluarga'],
            ['Saya merasa kondisi ekonomi keluarga mempengaruhi kehidupan saya', 'keluarga'],
            ['Saya merasa kurang mendapatkan dukungan dari keluarga',       'keluarga'],
            ['Saya merasa suasana rumah tidak nyaman bagi saya',            'keluarga'],
        ];

        foreach ($questions as [$text, $category]) {
            Question::create([
                'question_text' => $text,
                'category'      => $category,
                'is_active'     => true,
            ]);
        }
    }
}
