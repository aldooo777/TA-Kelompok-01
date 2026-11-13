<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Sprint 1 - Master data dan hak akses
|--------------------------------------------------------------------------
| program_studi        : master program studi IT Del
| users (diperluas)    : identitas pengguna dari CIS (tanpa password CIS)
| roles, user_roles    : peran di sistem PDLK. Mahasiswa dikenali dari CIS,
|                        sedangkan Staf UPT, Koordinator PDLK Prodi, dan
|                        Koordinator Program Studi diberikan melalui
|                        user_roles (dapat lebih dari satu peran).
| jenis_kegiatan_pdlk  : tujuh bentuk kegiatan PDLK
| mitra                : perusahaan / instansi tempat PDLK
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_studi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->string('jenjang', 10)->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('cis_user_id')->nullable()->unique()->after('id');
            $table->string('username', 100)->nullable()->unique()->after('cis_user_id');
            $table->string('nim', 30)->nullable()->index()->after('username');
            $table->string('cis_role', 50)->nullable()->after('email');
            $table->jsonb('cis_jabatan')->nullable()->after('cis_role');
            $table->foreignId('program_studi_id')->nullable()->after('cis_jabatan')
                ->constrained('program_studi')->nullOnDelete();
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });

        // Login memakai CIS sehingga password lokal tidak wajib.
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama', 100);
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            // Cakupan prodi untuk peran koordinator; null = seluruh institusi.
            $table->foreignId('program_studi_id')->nullable()
                ->constrained('program_studi')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'role_id', 'program_studi_id']);
        });

        Schema::create('jenis_kegiatan_pdlk', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('mitra', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 200)->unique();
            $table->string('bidang', 150)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->timestamps();
        });

        $this->isiDataReferensi();
    }

    /**
     * Data referensi yang dibutuhkan sistem sejak awal
     * (dan dipakai untuk memetakan data lama peserta_pdlk).
     */
    private function isiDataReferensi(): void
    {
        $now = now();

        DB::table('roles')->insert(array_map(
            fn ($r) => $r + ['created_at' => $now, 'updated_at' => $now],
            [
                ['kode' => 'mahasiswa', 'nama' => 'Mahasiswa'],
                ['kode' => 'staf_upt', 'nama' => 'Staf UPT PP-ESTEM'],
                ['kode' => 'koordinator_pdlk_prodi', 'nama' => 'Koordinator PDLK Program Studi'],
                ['kode' => 'koordinator_prodi', 'nama' => 'Koordinator Program Studi'],
            ]
        ));

        $jenis = [
            ['magang', 'Magang', 'Pembelajaran di industri atau lembaga yang relevan dengan program studi.'],
            ['kerja-praktik', 'Kerja Praktik', 'Penerapan ilmu dan keterampilan perkuliahan di lingkungan kerja nyata.'],
            ['penelitian', 'Penelitian / Riset', 'Kegiatan penelitian non-skripsi bagi mahasiswa yang berminat meneliti.'],
            ['pertukaran-pelajar', 'Pertukaran Pelajar', 'Perkuliahan di perguruan tinggi lain, di dalam maupun luar negeri.'],
            ['kkn', 'Kuliah Kerja Nyata (KKN)', 'Membangun desa bersama masyarakat selama satu sampai dua semester.'],
            ['studi-independen', 'Studi Independen', 'Mewujudkan karya atau produk inovatif yang dapat dilombakan.'],
            ['kewirausahaan', 'Wirausaha', 'Pengembangan usaha mahasiswa secara terbimbing.'],
        ];

        foreach ($jenis as $i => [$slug, $nama, $deskripsi]) {
            DB::table('jenis_kegiatan_pdlk')->insert([
                'slug' => $slug,
                'nama' => $nama,
                'deskripsi' => $deskripsi,
                'urutan' => $i + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $prodi = [
            ['D3TI', 'Teknologi Informasi', 'D3'],
            ['D3TK', 'Teknologi Komputer', 'D3'],
            ['D4TRPL', 'Teknologi Rekayasa Perangkat Lunak', 'D4'],
            ['S1IF', 'Informatika', 'S1'],
            ['S1SI', 'Sistem Informasi', 'S1'],
            ['S1TE', 'Teknik Elektro', 'S1'],
            ['S1MR', 'Manajemen Rekayasa', 'S1'],
            ['S1TB', 'Teknik Bioproses', 'S1'],
            ['S1BT', 'Bioteknologi', 'S1'],
            ['S1MT', 'Teknik Metalurgi', 'S1'],
        ];

        foreach ($prodi as [$kode, $nama, $jenjang]) {
            DB::table('program_studi')->insert([
                'kode' => $kode,
                'nama' => $nama,
                'jenjang' => $jenjang,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra');
        Schema::dropIfExists('jenis_kegiatan_pdlk');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_studi_id');
            $table->dropUnique(['cis_user_id']);
            $table->dropUnique(['username']);
            $table->dropIndex(['nim']);
            $table->dropColumn([
                'cis_user_id', 'username', 'nim', 'cis_role',
                'cis_jabatan', 'last_login_at',
            ]);
        });

        Schema::dropIfExists('program_studi');
    }
};
