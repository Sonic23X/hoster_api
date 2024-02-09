<?php

namespace App\Http\Requests\Properties;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('partner')) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|max:80',
            'address' => 'string|max:80',
            'longitude' => 'string',
            'latitude' => 'string',
            'rooms' => 'integer',
            'beds' => 'integer',
            'bathrooms' => 'integer',
            'about' => 'string|max:255',
            'additional_information' => 'string|max:255',
            'security' => 'string|max:255',
            'arrive' => 'string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'services' => 'array',
            'services.*' => 'string|max:40'
        ];
    }
}
