<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'              => ['required', 'array', 'min:1', 'max:10'],
            'items.*.surplus_id' => ['required', 'integer', 'exists:surplus_products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'              => 'Keranjang belanja kosong.',
            'items.*.surplus_id.exists'   => 'Produk surplus tidak ditemukan.',
            'items.*.quantity.min'        => 'Minimum pembelian 1 item.',
        ];
    }
}
