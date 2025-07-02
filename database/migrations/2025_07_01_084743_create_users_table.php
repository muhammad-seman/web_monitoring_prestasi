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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('username', 50)->unique();            // username unik (opsional, recommended)
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('role', [
                'admin',
                'guru',
                'siswa',
                'wali',
                'kepala_sekolah'
            ]);
            $table->enum('status', ['active', 'inactive'])->default('active'); // status aktif/nonaktif
            $table->unsignedBigInteger('kelas_id')->nullable()->comment('Hanya untuk guru/wali kelas');
            $table->unsignedBigInteger('siswa_id')->nullable()->comment('Hanya untuk user role siswa');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Soft delete, supaya data tidak benar-benar hilang

            // $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('set null');
            // $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('set null');
        });


        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        // Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
