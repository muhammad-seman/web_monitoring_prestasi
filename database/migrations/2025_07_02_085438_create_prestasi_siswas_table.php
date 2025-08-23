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
    Schema::create('prestasi_siswa', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_siswa');
        $table->unsignedBigInteger('id_kategori_prestasi');
        $table->unsignedBigInteger('id_tingkat_penghargaan');
        $table->unsignedBigInteger('id_ekskul')->nullable(); // Jika prestasi dari ekskul
        $table->unsignedBigInteger('id_tahun_ajaran')->nullable(); // Academic year tracking
        $table->string('nama_prestasi', 100);
        $table->string('penyelenggara', 100)->nullable();
        $table->date('tanggal_prestasi')->nullable();
        $table->text('keterangan')->nullable();
        $table->string('dokumen_url', 255)->nullable();  // File/URL sertifikat/piagam
        $table->enum('status', ['draft', 'menunggu_validasi', 'diterima', 'ditolak'])->default('draft');
        $table->decimal('rata_rata_nilai', 5,2)->nullable()->comment('Hanya diisi jika prestasi akademik');
        $table->unsignedBigInteger('created_by')->nullable();   // user_id (siswa)
        $table->unsignedBigInteger('validated_by')->nullable(); // user_id (guru)
        $table->timestamp('validated_at')->nullable();
        $table->text('alasan_tolak')->nullable();
        $table->timestamps();

        $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
        $table->foreign('id_kategori_prestasi')->references('id')->on('kategori_prestasi')->onDelete('restrict');
        $table->foreign('id_tingkat_penghargaan')->references('id')->on('tingkat_penghargaan')->onDelete('restrict');
        $table->foreign('id_ekskul')->references('id')->on('ekstrakurikuler')->onDelete('set null');
        $table->foreign('id_tahun_ajaran')->references('id')->on('tahun_ajaran')->onDelete('set null');
        $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi_siswa');
    }
};
