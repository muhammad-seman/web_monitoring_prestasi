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
        Schema::create('kategori_prestasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 50);
            $table->enum('jenis_prestasi', ['akademik', 'non_akademik'])->default('akademik');
            $table->enum('tingkat_kompetisi', ['sekolah', 'kabupaten', 'provinsi', 'nasional', 'internasional'])->nullable();
            $table->string('bidang_prestasi', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_prestasi');
    }
};
