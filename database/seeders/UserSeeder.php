<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin (Guru BK) ──────────────────────────────────────────────────
        User::create([
            'name'           => 'Riskie Relica, S.Pd',
            'email'          => 'admin@calmspace.com',
            'password'       => Hash::make('password'),
            'role'           => 'admin',
            'phone'          => '6282234567891',
            'specialization' => 'Konseling Akademik & Karier',
            'bio'            => 'Siap membantu menemukan potensi terbaikmu 🌿',
        ]);

        // ── Data Siswa & Orang Tua ───────────────────────────────────────────
        // Format: [nama_siswa, nisn, kelas, email_ortu, nama_ortu]
        $students = [
            ['AIGA BUNGA SAYVIANI',     '0096196341', 'XI-2', 'ortu.0096196341@calmspace.com', 'Orang Tua Aiga'],
            ['DINDA ARISKA',            '0099230190', 'XI-4', 'ortu.0099230190@calmspace.com', 'Orang Tua Dinda'],
            ['DESTI PRATIWI',           '0084190309', 'XI-1', 'ortu.0084190309@calmspace.com', 'Orang Tua Desti'],
            ['MARSELA',                 '0081485468', 'XI-4', 'ortu.0081485468@calmspace.com', 'Orang Tua Marsela'],
            ['AURA MIRANDA ANINDITA',   '3097954277', 'XI-2', 'ortu.3097954277@calmspace.com', 'Orang Tua Aura'],
            ['JIHAN AURELLYA LARASATI', '0094572439', 'XI-2', 'ortu.0094572439@calmspace.com', 'Orang Tua Jihan'],
            ['BUNGA HARUM PURNAMA',     '0091256569', 'XI-2', 'ortu.0091256569@calmspace.com', 'Orang Tua Bunga'],
            ['KEYRIN FETRIANI SYAHRAM', '0082838802', 'XI-3', 'ortu.0082838802@calmspace.com', 'Orang Tua Keyrin'],
            ['DIAH NUR KARIMAH',        '0093897836', 'XI-4', 'ortu.0093897836@calmspace.com', 'Orang Tua Diah'],
            ['SARAH AFIFAH',            '0091720232', 'XI-1', 'ortu.0091720232@calmspace.com', 'Orang Tua Sarah'],
            ['ADITIA PERATAMA',         '0096843665', 'XI-3', 'ortu.0096843665@calmspace.com', 'Orang Tua Aditia'],
            ['GHAZIAN DHANIS ALZAHY',   '0097214822', 'XI-2', 'ortu.0097214822@calmspace.com', 'Orang Tua Ghazian'],
            ['MARCEL PRADANA',          '0095330050', 'XI-4', 'ortu.0095330050@calmspace.com', 'Orang Tua Marcel'],
            ['SALU ARDIAN',             '0087460675', 'XI-2', 'ortu.0087460675@calmspace.com', 'Orang Tua Salu'],
            ['RISKI FADILA',            '0098075101', 'XI-3', 'ortu.0098075101@calmspace.com', 'Orang Tua Riski'],
            ['MAULANA SAKA IBRAHIM',    '0092672867', 'XI-4', 'ortu.0092672867@calmspace.com', 'Orang Tua Maulana'],
            ['MUHAMMAD FACHREZI YAHYA', '0094419983', 'XI-3', 'ortu.0094419983@calmspace.com', 'Orang Tua Fachrezi'],
            ['RAJA BOY',                '0091456905', 'XI-3', 'ortu.0091456905@calmspace.com', 'Orang Tua Raja'],
            ['VIAN',                    '0084776286', 'XI-3', 'ortu.0084776286@calmspace.com', 'Orang Tua Vian'],
            ['MUHAMMAD DIPA',           '0099007039', 'XI-3', 'ortu.0099007039@calmspace.com', 'Orang Tua Dipa'],
        ];

        foreach ($students as [$name, $nisn, $kelas, $emailOrtu, $namaOrtu]) {
            // 1. Buat akun orang tua terlebih dahulu
            $parent = User::create([
                'name'     => $namaOrtu,
                'email'    => $emailOrtu,
                'password' => Hash::make('password123'),
                'role'     => 'parent',
            ]);

            // 2. Buat akun siswa, hubungkan ke orang tua via parent_id
            User::create([
                'name'      => $name,
                'email'     => 'siswa.' . $nisn . '@calmspace.com',
                'nisn'      => $nisn,
                'kelas'     => $kelas,
                'role'      => 'student',
                'password'  => Hash::make('password123'),
                'parent_id' => $parent->id,
            ]);
        }
    }
}
