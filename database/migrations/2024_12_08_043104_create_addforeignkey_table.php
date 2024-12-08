<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan kolom foreign key `pengguna_id`.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('geographic_data', function (Blueprint $table) {
            $table->unsignedBigInteger('pengguna_id')->nullable()->after('id'); // Tambahkan kolom pengguna_id setelah id
            $table->foreign('pengguna_id')->references('id')->on('pengguna')->onDelete('cascade'); // Relasi ke tabel pengguna
        });
    }

    /**
     * Rollback migrasi dan menghapus kolom foreign key `pengguna_id`.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geographic_data', function (Blueprint $table) {
            $table->dropForeign(['pengguna_id']); // Hapus foreign key
            $table->dropColumn('pengguna_id'); // Hapus kolom pengguna_id
        });
    }
};
