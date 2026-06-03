<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'client_id' => ['required', 'exists:clients,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'event_date' => ['required', 'date'],
            'venue' => ['nullable', 'string', 'max:255'],
            'guest_count' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:Inquiry,Quoted,Confirmed,Completed,Cancelled'],
            'special_requirements' => ['nullable', 'string'],
        ];
    }
}
