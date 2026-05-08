<?php

namespace App\Http\Requests\Products;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProducts extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
                // unique per store (bukan global)
                Rule::unique('products', 'name')->where(function ($query) {
                    return $query->where('store_id', $this->store_id);
                }),
            ],

            'description' => ['nullable', 'string', 'max:1000'],

            'price' => ['required', 'integer', 'min:0'],

            'category_id' => ['required', 'exists:category_products,id'],

            'is_active' => ['nullable', 'boolean'],

            'images' => ['required', 'array', 'min:1', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            // NAME
            'name.required' => 'Nama produk wajib diisi.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.max' => 'Nama produk maksimal 150 karakter.',
            'name.unique' => 'Nama produk ini sudah digunakan di toko Anda. Silakan gunakan nama lain.',

            // DESCRIPTION
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',

            // PRICE
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh kurang dari 0.',

            // CATEGORY
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',

            // IS ACTIVE
            'is_active.boolean' => 'Status aktif tidak valid.',

            // IMAGES
            'images.required' => 'Gambar wajib diisi',
            'images.array' => 'Format gambar tidak valid.',
            'images.min' => 'Minimal harus upload 1 gambar.',
            'images.max' => 'Maksimal hanya boleh 8 gambar.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'images.*.max' => 'Ukuran setiap gambar maksimal 2MB.',
        ];
    }
}
