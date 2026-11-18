<?php

namespace App\Console\Commands;

use App\Support\Sql;
use Illuminate\Console\Command;

/**
 * Memberikan peran awal, misalnya akun Staf UPT pertama:
 *   php artisan pdlk:beri-peran if324018 upt_staff
 *   php artisan pdlk:beri-peran hernawati program_head --prodi=D3TI
 *   php artisan pdlk:beri-peran hernawati program_head --prodi=D3TI --cabut
 */
class BeriPeran extends Command
{
    protected $signature = 'pdlk:beri-peran
        {username : Username CIS}
        {peran : upt_staff | pdlk_coordinator | program_head}
        {--prodi= : Kode program studi (wajib untuk peran koordinator)}
        {--cabut : Cabut peran alih-alih memberikan}';

    protected $description = 'Memberikan atau mencabut peran PDLK untuk akun CIS';

    public function handle(): int
    {
        $role = Sql::one('SELECT id, code, name FROM roles WHERE code = ?', [$this->argument('peran')]);

        if (!$role || $role->code === 'student') {
            $this->error('Peran tidak valid. Pilih: upt_staff, pdlk_coordinator, program_head.');

            return self::FAILURE;
        }

        $prodiId = null;

        if ($this->option('prodi')) {
            $prodiId = Sql::value('SELECT id FROM study_programs WHERE code = ?', [strtoupper($this->option('prodi'))]);

            if (!$prodiId) {
                $this->error('Kode prodi tidak ditemukan. Tersedia: ' . Sql::all('SELECT code FROM study_programs ORDER BY code')->pluck('code')->implode(', '));

                return self::FAILURE;
            }
        } elseif ($role->code !== 'upt_staff') {
            $this->error('Peran koordinator wajib disertai --prodi=KODE.');

            return self::FAILURE;
        }

        $username = $this->argument('username');
        $userId = Sql::value('SELECT id FROM users WHERE username = ?', [$username])
            ?? Sql::insert('users', ['username' => $username, 'name' => $username]);

        if ($this->option('cabut')) {
            \DB::delete(
                'DELETE FROM user_roles WHERE user_id = ? AND role_id = ? AND study_program_id IS NOT DISTINCT FROM ?::bigint',
                [$userId, $role->id, $prodiId]
            );
            $this->info("Peran {$role->name} dicabut dari {$username}.");

            return self::SUCCESS;
        }

        \DB::insert(
            'INSERT INTO user_roles (user_id, role_id, study_program_id) VALUES (?, ?, ?) ON CONFLICT DO NOTHING',
            [$userId, $role->id, $prodiId]
        );
        $this->info("Peran {$role->name} diberikan kepada {$username}. Berlaku setelah login ulang.");

        return self::SUCCESS;
    }
}
