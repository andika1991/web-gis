<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel `pengguna` dengan kolom username dan password.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id(); // Primary key untuk tabel
            $table->string('username')->unique(); // Username unik
            $table->string('password'); // Password (hash)
            $table->timestamps(); // Kolom waktu (created_at dan updated_at)
        });
    }

    /**
     * Rollback migrasi dan menghapus tabel `pengguna`.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengguna'); // Menghapus tabel `pengguna`
    }
};
