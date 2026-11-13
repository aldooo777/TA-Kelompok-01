<?php

namespace App\Console\Commands;

use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Console\Command;

/**
 * Memberikan peran awal, misalnya akun Staf UPT pertama:
 *   php artisan pdlk:beri-peran monalisa staf_upt
 *   php artisan pdlk:beri-peran hernawati koordinator_prodi --prodi=D3TI
 *   php artisan pdlk:beri-peran hernawati koordinator_prodi --prodi=D3TI --cabut
 */
class BeriPeran extends Command
{
    protected $signature = 'pdlk:beri-peran
        {username : Username CIS}
        {peran : staf_upt | koordinator_pdlk_prodi | koordinator_prodi}
        {--prodi= : Kode program studi (wajib untuk peran koordinator)}
        {--cabut : Cabut peran alih-alih memberikan}';

    protected $description = 'Memberikan atau mencabut peran PDLK untuk akun CIS';

    public function handle(): int
    {
        $role = Role::where('kode', $this->argument('peran'))->first();

        if (!$role || $role->kode === Role::MAHASISWA) {
            $this->error('Peran tidak valid. Pilih: staf_upt, koordinator_pdlk_prodi, koordinator_prodi.');

            return self::FAILURE;
        }

        $prodiId = null;

        if ($this->option('prodi')) {
            $prodiId = ProgramStudi::where('kode', strtoupper($this->option('prodi')))->value('id');

            if (!$prodiId) {
                $this->error('Kode prodi tidak ditemukan. Tersedia: ' . ProgramStudi::pluck('kode')->implode(', '));

                return self::FAILURE;
            }
        } elseif ($role->kode !== Role::STAF_UPT) {
            $this->error('Peran koordinator wajib disertai --prodi=KODE.');

            return self::FAILURE;
        }

        $username = $this->argument('username');

        $user = User::firstOrCreate(
            ['username' => $username],
            ['name' => $username, 'email' => $username . '@belum-login.local']
        );

        $kunci = ['user_id' => $user->id, 'role_id' => $role->id, 'program_studi_id' => $prodiId];

        if ($this->option('cabut')) {
            UserRole::where($kunci)->delete();
            $this->info("Peran {$role->nama} dicabut dari {$username}.");

            return self::SUCCESS;
        }

        UserRole::firstOrCreate($kunci);
        $this->info("Peran {$role->nama} diberikan kepada {$username}. Berlaku setelah login ulang.");

        return self::SUCCESS;
    }
}
