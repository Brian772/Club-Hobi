<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClubRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name"        => "required|string|max:255",
            "hobby_id"    => "required|exists:hobbies,id",
            "description" => "nullable|string",
            "reason"      => "required|string|max:255",
            "cover"       => "nullable|image|mimes:jpeg,png|max:2048",
        ];
    }

    public function messages(): array
    {
        return [
            "name.required"     => "Nama klub wajib diisi.",
            "hobby_id.required" => "Kategori klub wajib dipilih.",
            "hobby_id.exists"   => "Kategori klub yang dipilih tidak valid.",
            "reason.required"   => "Alasan permintaan wajib diisi.",
            "cover.image"       => "File cover harus berupa gambar.",
            "cover.mimes"       => "File cover harus berupa file JPEG atau PNG.",
            "cover.max"         => "File cover tidak boleh lebih dari 2MB.",
        ];
    }
}
