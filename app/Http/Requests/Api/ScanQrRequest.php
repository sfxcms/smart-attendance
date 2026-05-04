<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ScanQrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qr_data' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'qr_data.required' => 'Data QR wajib diisi.',
            'qr_data.string' => 'Data QR harus berupa teks.',
        ];
    }
}
