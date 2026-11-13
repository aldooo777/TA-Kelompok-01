<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Sprint 2 & 3 - Mahasiswa aktif PDLK, progres, bukti, dan monev
|--------------------------------------------------------------------------
| peserta_pdlk = mahasiswa AKTIF PDLK. Sumber datanya:
|   - 'lowongan' : otomatis saat pendaftaran berstatus lulus
|   - 'mandiri'  : diinput koordinator PDLK prodi / staf UPT
| Mahasiswa tidak dapat memasukkan dirinya sendiri.
|
| Status peserta: aktif | tidak_aktif | selesai
|   - tidak ada fitur "batal magang"; tidak_aktif hanya untuk force majeure
|   - selesai ditetapkan otomatis setelah monev tengah dan akhir lengkap
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_pdlk', function (Blueprint $table) {
            $table->foreignId('pendaftaran_id')->nullable()->unique()
                ->constrained('pendaftaran')->nullOnDelete();
            $table->string('sumber', 20)->default('mandiri');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('program_studi_id')->nullable()->constrained('program_studi')->nullOnDelete();
            $table->foreignId('jenis_kegiatan_id')->nullable()->constrained('jenis_kegiatan_pdlk')->nullOnDelete();
            $table->foreignId('mitra_id')->nullable()->constrained('mitra')->nullOnDelete();
            $table->string('posisi', 150)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('status_pelaksanaan', 30)->nullable(); // onboarding|berjalan|selesai_kegiatan
            $table->string('mode_pelaksanaan', 20)->nullable();   // onsite|remote|hybrid
            $table->string('lokasi', 200)->nullable();
            $table->text('alasan_tidak_aktif')->nullable();
            $table->foreignId('diinput_oleh')->nullable()->constrained('users')->nullOnDelete();
        });

        $this->petakanDataLama();

        Schema::table('peserta_pdlk', function (Blueprint $table) {
            $table->renameColumn('mahasiswa_id', 'nim');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER INDEX IF EXISTS peserta_pdlk_mahasiswa_id_index RENAME TO peserta_pdlk_nim_index');
        }

        Schema::table('peserta_pdlk', function (Blueprint $table) {
            $table->dropColumn(['jenis_pdlk', 'tempat_magang']);
        });

        Schema::table('peserta_pdlk', function (Blueprint $table) {
            $table->string('status', 20)->default('aktif')->change();
        });

        Schema::create('riwayat_status_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_pdlk_id')->constrained('peserta_pdlk')->cascadeOnDelete();
            $table->string('status_lama', 20)->nullable();
            $table->string('status_baru', 20);
            $table->text('alasan')->nullable();
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('progres_pelaksanaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_pdlk_id')->constrained('peserta_pdlk')->cascadeOnDelete();
            $table->string('status_pelaksanaan', 30);
            $table->string('mode_pelaksanaan', 20);
            $table->string('lokasi', 200)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('bukti_pelaksanaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_pdlk_id')->constrained('peserta_pdlk')->cascadeOnDelete();
            $table->string('keterangan', 255);
            $table->date('tanggal_kegiatan')->nullable();
            $table->string('file_path');
            $table->timestamps();
        });

        Schema::create('kuesioner_monev', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 10); // tengah|akhir
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('kuesioner_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuesioner_monev_id')->constrained('kuesioner_monev')->cascadeOnDelete();
            $table->text('pertanyaan');
            $table->string('tipe', 20)->default('textarea'); // text|textarea|skala|pilihan
            $table->jsonb('opsi')->nullable();
            $table->boolean('wajib')->default(true);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('monev_pengisian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_pdlk_id')->constrained('peserta_pdlk')->cascadeOnDelete();
            $table->foreignId('kuesioner_monev_id')->constrained('kuesioner_monev')->restrictOnDelete();
            $table->string('jenis', 10); // tengah|akhir
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->unique(['peserta_pdlk_id', 'jenis']);
        });

        Schema::create('monev_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monev_pengisian_id')->constrained('monev_pengisian')->cascadeOnDelete();
            $table->foreignId('kuesioner_pertanyaan_id')->constrained('kuesioner_pertanyaan')->restrictOnDelete();
            $table->text('jawaban')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Data lama (registrations) memakai status menunggu/diterima/ditolak dan
     * menyimpan jenis serta tempat sebagai teks. Petakan ke struktur baru.
     */
    private function petakanDataLama(): void
    {
        $jenisIds = DB::table('jenis_kegiatan_pdlk')->pluck('id', 'slug');

        foreach (DB::table('peserta_pdlk')->get() as $row) {
            $mitraId = null;
            $namaMitra = trim((string) ($row->tempat_magang ?? ''));

            if ($namaMitra !== '') {
                $mitraId = DB::table('mitra')->where('nama', $namaMitra)->value('id')
                    ?? DB::table('mitra')->insertGetId([
                        'nama' => $namaMitra,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            $statusBaru = $row->status === 'diterima' ? 'aktif' : 'tidak_aktif';

            DB::table('peserta_pdlk')->where('id', $row->id)->update([
                'jenis_kegiatan_id' => $jenisIds[$row->jenis_pdlk] ?? null,
                'mitra_id' => $mitraId,
                'status' => $statusBaru,
                'alasan_tidak_aktif' => $statusBaru === 'tidak_aktif'
                    ? "Data lama berstatus '{$row->status}' sebelum perubahan alur."
                    : null,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('monev_jawaban');
        Schema::dropIfExists('monev_pengisian');
        Schema::dropIfExists('kuesioner_pertanyaan');
        Schema::dropIfExists('kuesioner_monev');
        Schema::dropIfExists('bukti_pelaksanaan');
        Schema::dropIfExists('progres_pelaksanaan');
        Schema::dropIfExists('riwayat_status_peserta');

        Schema::table('peserta_pdlk', function (Blueprint $table) {
            $table->string('jenis_pdlk', 50)->default('magang');
            $table->string('tempat_magang', 200)->default('-');
        });

        foreach (DB::table('peserta_pdlk')->get() as $row) {
            DB::table('peserta_pdlk')->where('id', $row->id)->update([
                'jenis_pdlk' => DB::table('jenis_kegiatan_pdlk')->where('id', $row->jenis_kegiatan_id)->value('slug') ?? 'magang',
                'tempat_magang' => DB::table('mitra')->where('id', $row->mitra_id)->value('nama') ?? '-',
                'status' => $row->status === 'aktif' ? 'diterima' : 'ditolak',
            ]);
        }

        Schema::table('peserta_pdlk', function (Blueprint $table) {
            $table->renameColumn('nim', 'mahasiswa_id');
            $table->string('status', 20)->default('menunggu')->change();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER INDEX IF EXISTS peserta_pdlk_nim_index RENAME TO peserta_pdlk_mahasiswa_id_index');
        }

        Schema::table('peserta_pdlk', function (Blueprint $table) {
            foreach ([
                'pendaftaran_id', 'user_id', 'program_studi_id',
                'jenis_kegiatan_id', 'mitra_id', 'diinput_oleh',
            ] as $fk) {
                $table->dropConstrainedForeignId($fk);
            }
            $table->dropColumn([
                'sumber', 'posisi', 'tanggal_mulai', 'tanggal_selesai',
                'status_pelaksanaan', 'mode_pelaksanaan', 'lokasi', 'alasan_tidak_aktif',
            ]);
        });
    }
};
