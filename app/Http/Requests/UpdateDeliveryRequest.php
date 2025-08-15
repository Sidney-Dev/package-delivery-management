<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statuses = ['pending', 'in_progress', 'completed', 'cancelled', 'scheduled'];

        return [
            'order_id'           => ['sometimes', 'exists:orders,id'],
            'assigned_driver_id' => ['sometimes', 'nullable', 'exists:drivers,id'],
            'status'             => ['sometimes', Rule::in($statuses)],
            'scheduled_at'       => ['sometimes', 'nullable', 'date'],
            'started_at'         => ['sometimes', 'nullable', 'date', 'after_or_equal:scheduled_at'],
            'completed_at'       => ['sometimes', 'nullable', 'date', 'after_or_equal:started_at', 'required_if:status,completed'],
            'city_id'            => ['sometimes', 'exists:cities,id'],
            'pickup_lat'         => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'pickup_lng'         => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Consistent JSON response for validation errors.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
