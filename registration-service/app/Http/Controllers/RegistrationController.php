<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Registration;
use Illuminate\Http\Request;


class RegistrationController extends Controller
{
    public function index()
{
    $registrations = Registration::all();

    return response()->json([
        'success' => true,
        'message' => 'Data pendaftaran berhasil diambil',
        'data' => $registrations,
    ]);
}

    public function store(StoreRegistrationRequest $request)
{
    $registration = Registration::create([
        'mahasiswa_id' => $request->mahasiswa_id,
        'nama' => $request->nama,
        'tempat_magang' => $request->tempat_magang,
        'status' => 'menunggu',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Pendaftaran magang berhasil diterima',
        'data' => $registration,
    ], 201);
}

    public function show($id)
{
    $registration = Registration::find($id);

    if (!$registration) {
        return response()->json([
            'success' => false,
            'message' => 'Data pendaftaran tidak ditemukan',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Data pendaftaran berhasil ditemukan',
        'data' => $registration,
    ]);
}

public function update(StoreRegistrationRequest $request, $id)
{
    $registration = Registration::find($id);

    if (!$registration) {
        return response()->json([
            'success' => false,
            'message' => 'Data pendaftaran tidak ditemukan',
        ], 404);
    }

    $registration->update([
        'mahasiswa_id' => $request->mahasiswa_id,
        'nama' => $request->nama,
        'tempat_magang' => $request->tempat_magang,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Data pendaftaran berhasil diperbarui',
        'data' => $registration,
    ]);
}

public function destroy($id)
{
    $registration = Registration::find($id);

    if (!$registration) {
        return response()->json([
            'success' => false,
            'message' => 'Data pendaftaran tidak ditemukan',
        ], 404);
    }

    $registration->delete();

    return response()->json([
        'success' => true,
        'message' => 'Data pendaftaran berhasil dihapus',
    ]);
}

}
