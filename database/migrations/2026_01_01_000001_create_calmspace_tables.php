<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Personal Access Tokens (Sanctum) ─────────────────────────────────
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });

        // ── Pertanyaan Asesmen ───────────────────────────────────────────────
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Riwayat Screening ────────────────────────────────────────────────
        Schema::create('screening_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('total_score');
            $table->float('grand_mean')->nullable();
            $table->string('conclusion');
            $table->timestamps();
        });

        // ── Aktivitas Harian ─────────────────────────────────────────────────
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->date('activity_date');
            $table->string('title');
            $table->text('description');
            $table->enum('mood', ['happy', 'sad', 'neutral', 'angry'])->nullable();
            $table->timestamps();
        });

        // ── Jurnal Harian ────────────────────────────────────────────────────
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->string('mood');
            $table->string('mood_label');
            $table->string('date');
            $table->timestamps();
        });

        // ── Konsultasi ───────────────────────────────────────────────────────
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('topic');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });

        // ── Pesan Konsultasi ─────────────────────────────────────────────────
        Schema::create('consultation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->timestamps();
        });

        // ── Pesan Chat Langsung ──────────────────────────────────────────────
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('consultation_messages');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('journals');
        Schema::dropIfExists('daily_activities');
        Schema::dropIfExists('screening_histories');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('personal_access_tokens');
    }
};
