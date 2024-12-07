<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeographicDataTable extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geographic_data', function (Blueprint $table) {
            $table->id(); // ID unik untuk setiap data geografis
            $table->string('name'); // Nama lokasi/objek geografis
            $table->enum('type', ['Point', 'LineString', 'Polygon']); // Tipe data geospasial
            $table->json('coordinates'); // Kolom untuk menyimpan data GeoJSON (Point, LineString, Polygon)
            $table->text('description')->nullable(); // Deskripsi (opsional)
            $table->timestamps(); // Kolom waktu (created_at, updated_at)
        });
    }

    /**
     * Rollback migrasi dan menghapus tabel.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geographic_data');
    }
}
