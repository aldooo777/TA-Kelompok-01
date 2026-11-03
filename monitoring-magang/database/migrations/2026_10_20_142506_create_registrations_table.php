
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();

            // Identitas mahasiswa dari CIS.
            $table->string('mahasiswa_id', 50);
            $table->string('nama', 150);

            // Jenis kegiatan PDLK yang dipilih.
            $table->string('jenis_pdlk', 50);

            // Tempat pelaksanaan kegiatan.
            $table->string('tempat_magang', 200);

            // Status awal pendaftaran.
            $table->string('status', 20)->default('menunggu');

            $table->timestamps();

            // Index untuk membantu pencarian data.
            $table->index('mahasiswa_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
