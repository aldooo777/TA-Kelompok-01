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
    return $this->registrationService->create(
        $request->all()
    );
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
