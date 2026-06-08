<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screening_answers', function (Blueprint $table) {
            $table->id();
            // Relasi ke riwayat screening (induknya)
            $table->foreignId('screening_history_id')->constrained()->onDelete('cascade');
            // Relasi ke soal apa yang dijawab
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            // Skor yang dipilih siswa untuk soal ini (misal: 0, 1, 2, atau 3)
            $table->integer('score'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screening_answers');
    }
};