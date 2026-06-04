<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('screening_histories', function (Blueprint $table) {
        // Pakai float atau double untuk menyimpan angka desimal (rata-rata)
        $table->float('grand_mean')->after('total_score')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('screening_histories', function (Blueprint $table) {
            //
        });
    }
};
