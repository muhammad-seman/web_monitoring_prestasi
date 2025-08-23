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
        Schema::table('kategori_prestasi', function (Blueprint $table) {
            $table->enum('jenis_prestasi', ['akademik', 'non_akademik'])->default('akademik')->after('nama_kategori');
            $table->enum('tingkat_kompetisi', ['sekolah', 'kabupaten', 'provinsi', 'nasional', 'internasional'])->nullable()->after('jenis_prestasi');
            $table->string('bidang_prestasi', 50)->nullable()->after('tingkat_kompetisi')->comment('olahraga, seni, lomba, organisasi');
            $table->text('deskripsi')->nullable()->after('bidang_prestasi');
            $table->boolean('is_active')->default(true)->after('deskripsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_prestasi', function (Blueprint $table) {
            $table->dropColumn(['jenis_prestasi', 'tingkat_kompetisi', 'bidang_prestasi', 'deskripsi', 'is_active']);
        });
    }
};
