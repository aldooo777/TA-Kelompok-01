<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mahasiswa_id' => [
                'required',
                'string',
                'max:50',
            ],

            'nama' => [
                'required',
                'string',
                'max:150',
            ],

            'tempat_magang' => [
                'required',
                'string',
                'max:200',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'mahasiswa_id.required' => 'ID mahasiswa wajib diisi.',
            'nama.required' => 'Nama mahasiswa wajib diisi.',
            'tempat_magang.required' => 'Tempat magang wajib diisi.',
        ];
    }
}
