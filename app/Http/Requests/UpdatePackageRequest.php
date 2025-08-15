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
            'delivery_id' => 'required|exists:deliveries,id',
            'sku'         => "required|string|max:50|unique:packages",
            'weight'      => 'required|numeric|min:0.01',
            'dimensions'  => 'nullable|string|max:255',
            'status'      => 'required|string|max:50',
            'return_reason' => 'nullable|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'city_id'     => 'required|exists:cities,id',
        ];
    }
}
