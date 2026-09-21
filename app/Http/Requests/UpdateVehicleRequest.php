<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand' => 'sometimes|string|max:255',
            'model' => 'sometimes|string|max:255',
            'year' => 'sometimes|integer|digits:4',
            'plate' => [
                'sometimes', 'string', 'max:7',
                Rule::unique('vehicles', 'plate')->ignore($this->route('id')),
            ],
        ];
    }
}
