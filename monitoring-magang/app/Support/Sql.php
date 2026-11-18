<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Akses database dengan query SQL langsung (tanpa Eloquent).
 *
 *   Sql::all('SELECT * FROM partners WHERE is_active = ? ORDER BY name', [true]);
 *   Sql::one('SELECT * FROM partners WHERE id = ?', [$id]);
 *   Sql::insert('partners', ['name' => 'PT A']);      // INSERT ... RETURNING id
 *   Sql::update('partners', $id, ['name' => 'PT B']); // UPDATE ... WHERE id = ?
 *
 * insert/update otomatis mengisi kolom audit created_by / updated_by
 * dengan pengguna yang sedang login, serta updated_at.
 */
class Sql
{
    /** @return Collection<int, object> */
    public static function all(string $sql, array $bindings = []): Collection
    {
        return collect(DB::select($sql, $bindings));
    }

    public static function one(string $sql, array $bindings = []): ?object
    {
        return DB::selectOne($sql, $bindings);
    }

    public static function value(string $sql, array $bindings = []): mixed
    {
        $row = DB::selectOne($sql, $bindings);

        return $row ? array_values((array) $row)[0] : null;
    }

    public static function oneOrFail(string $sql, array $bindings = []): object
    {
        return self::one($sql, $bindings) ?? abort(404);
    }

    /** INSERT INTO $table (...) VALUES (...) RETURNING id */
    public static function insert(string $table, array $data): int
    {
        $userId = Peran::userId();
        $data += ['created_by' => $userId, 'updated_by' => $userId];
        $data = self::encode($data);

        $kolom = implode(', ', array_map(fn ($k) => self::ident($k), array_keys($data)));
        $tanda = implode(', ', array_fill(0, count($data), '?'));

        $row = DB::selectOne(
            'INSERT INTO ' . self::ident($table) . " ($kolom) VALUES ($tanda) RETURNING id",
            array_values($data)
        );

        return (int) $row->id;
    }

    /** UPDATE $table SET ..., updated_at = CURRENT_TIMESTAMP, updated_by = ? WHERE id = ? */
    public static function update(string $table, int $id, array $data): int
    {
        $data['updated_by'] = Peran::userId();
        $data = self::encode($data);

        $set = implode(', ', array_map(fn ($k) => self::ident($k) . ' = ?', array_keys($data)));

        return DB::update(
            'UPDATE ' . self::ident($table) . " SET $set, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
            [...array_values($data), $id]
        );
    }

    /** Hapus lunak: is_active = false (data tetap ada di database). */
    public static function deactivate(string $table, int $id): int
    {
        return self::update($table, $id, ['is_active' => false]);
    }

    public static function delete(string $table, int $id): int
    {
        return DB::delete('DELETE FROM ' . self::ident($table) . ' WHERE id = ?', [$id]);
    }

    /**
     * Paginasi untuk query SELECT bebas.
     * $sql tidak boleh berisi LIMIT/OFFSET.
     */
    public static function paginate(string $sql, array $bindings = [], int $perPage = 20): LengthAwarePaginator
    {
        $page = max(1, (int) request()->query('page', 1));
        $total = (int) self::value("SELECT COUNT(*) FROM ($sql) AS hitung", $bindings);
        $rows = self::all("$sql LIMIT $perPage OFFSET " . (($page - 1) * $perPage), $bindings);

        return (new LengthAwarePaginator($rows, $total, $perPage, $page, [
            'path' => request()->url(),
        ]))->withQueryString();
    }

    public static function transaction(callable $fn): mixed
    {
        return DB::transaction($fn);
    }

    /** Ubah array menjadi JSON untuk kolom jsonb. */
    private static function encode(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = json_encode($v, JSON_UNESCAPED_UNICODE);
            } elseif (is_bool($v)) {
                $data[$k] = $v ? 'true' : 'false';
            }
        }

        return $data;
    }

    private static function ident(string $nama): string
    {
        if (!preg_match('/^[a-z_][a-z0-9_]*$/', $nama)) {
            throw new \InvalidArgumentException("Nama kolom/tabel tidak valid: $nama");
        }

        return '"' . $nama . '"';
    }
}
