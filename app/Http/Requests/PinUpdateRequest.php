<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PinUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'current_pin' => ['required', 'string', 'max:4', 'min:4', 'regex:/^[0-9]+$/'],
            'new_pin' => ['required', 'string', 'max:4', 'min:4', 'regex:/^[0-9]+$/', 'different:current_pin'],
            'confirm_pin' => ['required', 'same:new_pin'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'current_pin.required' => 'Current PIN is required.',
            'current_pin.regex' => 'Current PIN must contain only numbers.',
            'current_pin.min' => 'Current PIN must be exactly 4 digits.',
            'current_pin.max' => 'Current PIN must be exactly 4 digits.',
            'new_pin.required' => 'New PIN is required.',
            'new_pin.regex' => 'New PIN must contain only numbers.',
            'new_pin.min' => 'New PIN must be exactly 4 digits.',
            'new_pin.max' => 'New PIN must be exactly 4 digits.',
            'new_pin.different' => 'New PIN must be different from current PIN.',
            'confirm_pin.required' => 'PIN confirmation is required.',
            'confirm_pin.same' => 'PIN confirmation does not match.',
        ];
    }
} 