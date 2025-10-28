<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMahasiswaRequest extends FormRequest
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
            'npm' => ['required', 'string', 'max:20', 'unique:mahasiswa,npm', 'regex:/^[A-Z0-9]+$/'],
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s.]+$/'],
            'prodi' => ['required', 'string', 'max:255'],
            'fakultas' => ['required', 'string', 'max:255'],
            'ipk' => ['required', 'numeric', 'between:0,4', 'regex:/^\d+(\.\d{1,2})?$/'],
            'yudisium' => ['nullable', 'string', 'in:Cum Laude,Sangat Memuaskan,Memuaskan'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
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
            'npm.required' => 'NPM wajib diisi',
            'npm.unique' => 'NPM sudah terdaftar',
            'npm.regex' => 'NPM hanya boleh berisi huruf kapital dan angka',
            'nama.required' => 'Nama wajib diisi',
            'nama.regex' => 'Nama hanya boleh berisi huruf dan spasi',
            'prodi.required' => 'Program studi wajib diisi',
            'fakultas.required' => 'Fakultas wajib diisi',
            'ipk.required' => 'IPK wajib diisi',
            'ipk.numeric' => 'IPK harus berupa angka',
            'ipk.between' => 'IPK harus antara 0.00 dan 4.00',
            'ipk.regex' => 'IPK harus berupa angka dengan maksimal 2 desimal',
            'yudisium.in' => 'Yudisium harus salah satu dari: Cum Laude, Sangat Memuaskan, Memuaskan',
            'email.email' => 'Format email tidak valid',
            'phone.regex' => 'Format nomor telepon tidak valid',
        ];
    }
}
