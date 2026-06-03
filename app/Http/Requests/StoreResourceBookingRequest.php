<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreResourceBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resource_id' => ['required', 'exists:resources,id'],
            'event_id' => ['required', 'exists:events,id'],
            'booking_date' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:Reserved,Released,Cancelled'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
