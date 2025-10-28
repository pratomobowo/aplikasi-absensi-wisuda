<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGraduationEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'location_name' => ['required', 'string', 'max:255'],
            'location_address' => ['required', 'string'],
            'location_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'location_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama acara wajib diisi',
            'date.required' => 'Tanggal acara wajib diisi',
            'date.after_or_equal' => 'Tanggal acara tidak boleh di masa lalu',
            'time.required' => 'Waktu acara wajib diisi',
            'time.date_format' => 'Format waktu tidak valid (gunakan HH:MM)',
            'location_name.required' => 'Nama lokasi wajib diisi',
            'location_address.required' => 'Alamat lokasi wajib diisi',
            'location_lat.between' => 'Latitude harus antara -90 dan 90',
            'location_lng.between' => 'Longitude harus antara -180 dan 180',
        ];
    }
}
