<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Sprint 1 - Informasi, lowongan, dan pendaftaran
|--------------------------------------------------------------------------
| postingan                : berita / pengumuman / kegiatan (foto + narasi)
| lowongan                 : kesempatan PDLK dari mitra beserta kriterianya
| lowongan_field_tambahan  : isian tambahan (tidak wajib) sesuai permintaan
|                            mitra, ditambahkan oleh Staf UPT per lowongan
| pendaftaran              : formulir satu format untuk semua lowongan.
|                            Status: terdaftar -> proses -> lulus / tidak_lulus.
|                            Seleksi dilakukan mitra di luar sistem.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postingan', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 20)->default('berita'); // berita|pengumuman|kegiatan
            $table->string('judul', 200);
            $table->string('slug', 220)->unique();
            $table->text('narasi');
            $table->string('foto_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
        });

        Schema::create('lowongan', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->string('slug', 220)->unique();
            $table->foreignId('mitra_id')->constrained('mitra')->restrictOnDelete();
            $table->foreignId('jenis_kegiatan_id')->constrained('jenis_kegiatan_pdlk')->restrictOnDelete();
            $table->string('posisi', 150)->nullable();
            $table->string('lokasi', 150)->nullable();
            $table->text('deskripsi');
            $table->text('kriteria');
            $table->unsignedInteger('kuota')->nullable();
            $table->date('tanggal_buka')->nullable();
            $table->date('tanggal_tutup')->nullable();
            $table->string('poster_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_published', 'tanggal_tutup']);
        });

        Schema::create('lowongan_field_tambahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lowongan_id')->constrained('lowongan')->cascadeOnDelete();
            $table->string('label', 150);
            $table->string('tipe', 20)->default('text'); // text|textarea|number|url
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lowongan_id')->constrained('lowongan')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nim', 30);
            $table->string('nama', 150);
            $table->foreignId('program_studi_id')->constrained('program_studi')->restrictOnDelete();
            $table->unsignedTinyInteger('semester');
            $table->decimal('ipk', 3, 2);
            $table->string('no_hp', 20);
            $table->string('email', 150);
            // CV, portofolio, surat keterangan, transkrip/KHS digabung jadi satu file.
            $table->string('dokumen_path');
            $table->string('dokumen_nama_asli', 255);
            $table->jsonb('jawaban_tambahan')->nullable();
            $table->string('status', 20)->default('terdaftar');
            $table->text('catatan')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diproses_at')->nullable();
            $table->timestamps();

            $table->unique(['lowongan_id', 'nim']);
            $table->index('nim');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
        Schema::dropIfExists('lowongan_field_tambahan');
        Schema::dropIfExists('lowongan');
        Schema::dropIfExists('postingan');
    }
};
