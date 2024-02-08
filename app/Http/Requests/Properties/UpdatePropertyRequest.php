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
            'title' => 'required|string|max:80',
            'address' => 'required|string|max:80',
            'rooms' => 'required|integer',
            'beds' => 'required|integer',
            'bathrooms' => 'required|integer',
            'about' => 'required|string|max:255',
            'additional_information' => 'required|string|max:255',
            'security' => 'required|string|max:255',
            'arrive' => 'required|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'services' => 'array',
            'services.*' => 'string|max:40'
        ];
    }
}
