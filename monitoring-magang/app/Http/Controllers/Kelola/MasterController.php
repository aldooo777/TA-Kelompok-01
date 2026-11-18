<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Support\MasterData;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * CRUD data master dengan query SQL langsung.
 * Tabel: study_programs, periods, activity_types, partners,
 * organization_positions, contacts, roles (lihat App\Support\MasterData).
 */
class MasterController extends Controller
{
    public function index(Request $request, string $master)
    {
        $def = MasterData::get($master);
        $q = trim((string) $request->query('q'));
        $kolomCari = collect($def['fields'])->filter(fn ($f) => in_array($f['type'], ['text', 'url'], true))->keys();

        $where = [];
        $bind = [];
        if ($q !== '' && $kolomCari->isNotEmpty()) {
            $where[] = '(' . $kolomCari->map(fn ($k) => "LOWER(COALESCE($k, '')) LIKE ?")->implode(' OR ') . ')';
            $bind = array_fill(0, $kolomCari->count(), '%' . mb_strtolower($q) . '%');
        }
        if ($request->query('status') === 'nonaktif') {
            $where[] = 'is_active = false';
        } elseif ($request->query('status') !== 'semua') {
            $where[] = 'is_active = true';
        }

        $sql = "SELECT * FROM {$def['table']}"
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
            . " ORDER BY {$def['order']}";

        return view('kelola.master.index', [
            'def' => $def,
            'rows' => Sql::paginate($sql, $bind, 25),
        ]);
    }

    public function create(string $master)
    {
        $def = MasterData::get($master);
        abort_if($def['no_create'] ?? false, 404);

        return view('kelola.master.form', ['def' => $def, 'row' => null]);
    }

    public function store(Request $request, string $master)
    {
        $def = MasterData::get($master);
        abort_if($def['no_create'] ?? false, 404);

        Sql::insert($def['table'], $this->data($request, $def));

        return redirect()->route('kelola.master.index', $master)->with('success', "{$def['title']} berhasil ditambahkan.");
    }

    public function edit(string $master, int $id)
    {
        $def = MasterData::get($master);
        $row = Sql::oneOrFail("SELECT * FROM {$def['table']} WHERE id = ?", [$id]);

        return view('kelola.master.form', compact('def', 'row'));
    }

    public function update(Request $request, string $master, int $id)
    {
        $def = MasterData::get($master);
        $row = Sql::oneOrFail("SELECT * FROM {$def['table']} WHERE id = ?", [$id]);

        Sql::update($def['table'], $id, $this->data($request, $def, $row));

        return redirect()->route('kelola.master.index', $master)->with('success', "{$def['title']} berhasil diperbarui.");
    }

    /** Aktif / nonaktifkan (is_active). Data tidak dihapus permanen. */
    public function toggle(string $master, int $id)
    {
        $def = MasterData::get($master);
        $row = Sql::oneOrFail("SELECT id, is_active FROM {$def['table']} WHERE id = ?", [$id]);

        Sql::update($def['table'], $id, ['is_active' => !$row->is_active]);

        return back()->with('success', $row->is_active ? 'Data dinonaktifkan.' : 'Data diaktifkan kembali.');
    }

    private function data(Request $request, array $def, ?object $row = null): array
    {
        $rules = [];
        foreach ($def['fields'] as $kolom => $f) {
            if ($f['readonly'] ?? false) {
                continue;
            }
            $r = [($f['required'] ?? false) ? 'required' : 'nullable'];
            $r = array_merge($r, match ($f['type']) {
                'number' => ['integer', 'min:' . ($f['min'] ?? 0)],
                'date' => ['date'],
                'url' => ['url'],
                'enum' => [Rule::in(array_keys($f['options']))],
                'image' => ['image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
                default => ['string'],
            });
            if (isset($f['max']) && $f['type'] !== 'image') {
                $r[] = 'max:' . $f['max'];
            }
            if ($f['unique'] ?? false) {
                $r[] = Rule::unique($def['table'], $kolom)->ignore($row?->id);
            }
            if (isset($f['after'])) {
                $r[] = 'after_or_equal:' . $f['after'];
            }
            $rules[$kolom] = $r;
        }

        $label = collect($def['fields'])->mapWithKeys(fn ($f, $k) => [$k => strtolower($f['label'])])->all();
        $valid = $request->validate($rules, [], $label);

        foreach ($def['fields'] as $kolom => $f) {
            if ($f['type'] !== 'image') {
                continue;
            }
            unset($valid[$kolom]);
            if ($request->hasFile($kolom)) {
                if ($row?->$kolom) {
                    Storage::disk('public')->delete($row->$kolom);
                }
                $valid[$kolom] = $request->file($kolom)->store($f['folder'], 'public');
            } elseif ($request->boolean("hapus_$kolom") && $row?->$kolom) {
                Storage::disk('public')->delete($row->$kolom);
                $valid[$kolom] = null;
            }
        }

        return $valid;
    }
}
