<?php

namespace App\Http\Requests\Cart;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'surplus_id' => ['required', 'integer', 'exists:surplus_products,id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'surplus_id.required' => 'Produk surplus tidak valid.',
            'surplus_id.exists'   => 'Produk surplus tidak ditemukan.',
            'quantity.required'   => 'Jumlah harus diisi.',
            'quantity.min'        => 'Jumlah minimal 1.',
            'quantity.max'        => 'Jumlah maksimal 100 per item.',
        ];
    }
}
