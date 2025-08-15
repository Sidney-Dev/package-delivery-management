<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_id'   => 'required|exists:deliveries,id',
            'sku'           => 'required|string|max:50|unique:packages,sku',
            'weight'        => 'required|numeric|min:0.01',
            'dimensions'    => 'nullable|string|max:255',
            'status'        => 'required|string|max:50',
            'return_reason' => 'nullable|string|max:255',
            'customer_id'   => 'required|exists:users,id',
            'city_id'       => 'required|exists:cities,id',
        ];
    }

    /**
     * Customize the response for failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors'  => $errors
        ], 422));
    }
}
