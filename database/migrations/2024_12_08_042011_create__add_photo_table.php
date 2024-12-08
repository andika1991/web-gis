<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan kolom `photo`.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('geographic_data', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('description'); // Tambahkan kolom photo setelah kolom description
        });
    }

    /**
     * Rollback migrasi dan menghapus kolom `photo`.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geographic_data', function (Blueprint $table) {
            if (Schema::hasColumn('geographic_data', 'photo')) {
                $table->dropColumn('photo'); // Hapus kolom photo jika ada
            }
        });
    }
};
