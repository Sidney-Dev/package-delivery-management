<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_no'         => 'required|string|max:50|unique:orders,order_no',
            'customer_id'      => 'required|exists:users,id',
            'pickup_address'   => 'required|string|max:255',
            'dropoff_address'  => 'required|string|max:255',
            'city_id'          => 'required|exists:cities,id',
            'status'           => 'nullable|in:pending,in_progress,completed,cancelled',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
