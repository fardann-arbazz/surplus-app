<?php

namespace App\Http\Requests\Location;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
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
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required'  => 'Koordinat latitude wajib diisi.',
            'latitude.between'   => 'Koordinat latitude tidak valid.',
            'longitude.required' => 'Koordinat longitude wajib diisi.',
            'longitude.between'  => 'Koordinat longitude tidak valid.',
        ];
    }
}
