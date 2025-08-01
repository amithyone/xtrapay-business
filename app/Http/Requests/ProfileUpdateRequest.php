<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'bvn' => ['nullable', 'string', 'max:11', 'min:11', 'regex:/^[0-9]+$/'],
            'date_of_birth' => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
            'pin' => ['nullable', 'string', 'max:4', 'min:4', 'regex:/^[0-9]+$/'],
            'gender' => ['nullable', 'in:male,female,other'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'bvn.regex' => 'BVN must contain only numbers.',
            'bvn.min' => 'BVN must be exactly 11 digits.',
            'bvn.max' => 'BVN must be exactly 11 digits.',
            'pin.regex' => 'PIN must contain only numbers.',
            'pin.min' => 'PIN must be exactly 4 digits.',
            'pin.max' => 'PIN must be exactly 4 digits.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'date_of_birth.after' => 'Date of birth must be after 1900.',
            'profile_photo.max' => 'Profile photo must not exceed 2MB.',
        ];
    }
} 