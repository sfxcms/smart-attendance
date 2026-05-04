<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schedule_id' => 'required|integer|exists:schedules,id',
            'tipe_sesi' => 'sometimes|in:online,offline',
            'link_meeting' => 'required_if:tipe_sesi,online|nullable|url|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required' => 'Jadwal wajib dipilih.',
            'schedule_id.integer' => 'ID jadwal tidak valid.',
            'schedule_id.exists' => 'Jadwal tidak ditemukan.',
            'tipe_sesi.in' => 'Tipe sesi harus online atau offline.',
            'link_meeting.required_if' => 'Link meeting wajib diisi untuk sesi online.',
            'link_meeting.url' => 'Link meeting harus berupa URL yang valid.',
        ];
    }
}
