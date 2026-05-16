<?php

namespace App\Http\Requests\SurplusProduct;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSurplusProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'initial_price' => [
                'required',
                'integer',
                'min:0',
            ],

            'discount_price' => [
                'required',
                'integer',
                'min:0',
                'lte:initial_price',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'expired_at' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'pickup_start_at' => [
                'required',
                'date_format:H:i:s',
                'before_or_equal:pickup_end_at',
            ],

            'pickup_end_at' => [
                'required',
                'date_format:H:i:s',
                'after_or_equal:pickup_start_at',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // price
            'initial_price.required' => 'Harga awal wajib diisi.',
            'initial_price.integer' => 'Harga awal harus berupa angka.',
            'initial_price.min' => 'Harga awal tidak boleh negatif.',

            'discount_price.required' => 'Harga diskon wajib diisi.',
            'discount_price.integer' => 'Harga diskon harus berupa angka.',
            'discount_price.min' => 'Harga diskon tidak boleh negatif.',
            'discount_price.lte' => 'Harga diskon tidak boleh lebih besar dari harga awal.',

            // quantity
            'quantity.required' => 'Jumlah produk wajib diisi.',
            'quantity.integer' => 'Jumlah produk harus berupa angka.',
            'quantity.min' => 'Jumlah produk minimal 1.',

            // expired
            'expired_at.required' => 'Tanggal kadaluarsa wajib diisi.',
            'expired_at.date' => 'Format tanggal kadaluarsa tidak valid.',
            'expired_at.after_or_equal' => 'Tanggal kadaluarsa tidak boleh sebelum hari ini.',

            // pickup start
            'pickup_start_at.required' => 'Jam mulai pengambilan wajib diisi.',
            'pickup_start_at.date_format' => 'Format jam mulai tidak valid.',
            'pickup_start_at.before_or_equal' => 'Jam mulai harus sebelum atau sama dengan jam selesai.',

            // pickup end
            'pickup_end_at.required' => 'Jam selesai pengambilan wajib diisi.',
            'pickup_end_at.date_format' => 'Format jam selesai tidak valid.',
            'pickup_end_at.after_or_equal' => 'Jam selesai harus setelah atau sama dengan jam mulai.',
        ];
    }
}
