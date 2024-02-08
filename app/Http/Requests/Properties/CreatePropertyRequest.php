<?php

namespace App\Http\Requests\Properties;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreatePropertyRequest extends FormRequest
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
            'owner_id' => 'exists:App\Models\User,uuid',
            'title' => 'required|string|max:80',
            'address' => 'required|string|max:80',
            'rooms' => 'required|integer',
            'beds' => 'required|integer',
            'bathrooms' => 'required|integer',
            'about' => 'required|string',
            'additional_information' => 'required|string',
            'security' => 'required|string',
            'arrive' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'services' => 'array',
            'services.*' => 'string|max:40'
        ];
    }
}
