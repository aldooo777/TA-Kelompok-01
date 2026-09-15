<?php

namespace App\Http\Controllers;

use App\Services\RegistrationService;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    protected RegistrationService $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function create()
{
    return view('registration.create');
}

    public function index()
    {
        $response = $this->registrationService->getAll();

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function show($id)
    {
        $response = $this->registrationService->getById($id);

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

   public function store(Request $request)
{
    $cisUser = session('cis_user');

    $data = [
        'mahasiswa_id' => $cisUser['nim'] ?? null,
        'tempat_magang' => $request->input('tempat_magang'),
    ];

    $response = $this->registrationService->create($data);

    if ($response->successful()) {
        return redirect()
            ->route('registration.create')
            ->with('success', $response->json('message'));
    }

    return back()
        ->withErrors([
            'registration' => $response->json('message') ?? 'Pendaftaran magang gagal.',
        ])
        ->withInput();
}

    public function update(Request $request, $id)
    {
        $response = $this->registrationService->update(
            $id,
            $request->all()
        );

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function destroy($id)
    {
        $response = $this->registrationService->delete($id);

        return response()->json(
            $response->json(),
            $response->status()
        );
    }
}
