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
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('set null');
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->foreign('id_wali_kelas')->references('id')->on('users')->onDelete('set null');
        });
    }
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
        });
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['id_wali_kelas']);
        });
    }
};
