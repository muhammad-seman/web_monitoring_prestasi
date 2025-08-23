<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswa_ekskul', function (Blueprint $table) {
            $table->string('tahun_ajaran', 10)->nullable()->after('id_ekskul')->comment('Format: 2024/2025');
            $table->enum('semester', ['ganjil', 'genap'])->nullable()->after('tahun_ajaran');
            $table->date('tanggal_mulai')->nullable()->after('semester');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            $table->enum('status_keaktifan', ['aktif', 'non_aktif', 'graduated'])->default('aktif')->after('tanggal_selesai');
            $table->text('keterangan')->nullable()->after('status_keaktifan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_ekskul', function (Blueprint $table) {
            $table->dropColumn(['tahun_ajaran', 'semester', 'tanggal_mulai', 'tanggal_selesai', 'status_keaktifan', 'keterangan']);
        });
    }
};
