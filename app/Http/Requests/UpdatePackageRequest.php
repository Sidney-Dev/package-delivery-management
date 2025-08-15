<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_id' => 'nullable|exists:deliveries,id',
            'sku'         => "nullable|string|max:50|unique:packages",
            'weight'      => 'nullable|numeric|min:0.01',
            'dimensions'  => 'nullable|string|max:255',
            'status'      => 'nullable|string|max:50',
            'return_reason' => 'nullable|string|max:255',
            'customer_id' => 'nullable|exists:customers,id',
            'city_id'     => 'nullable|exists:cities,id',
        ];
    }
}
