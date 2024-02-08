<?php

namespace App\Http\Requests\Users;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
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
        $url = $this->url();
        $uuid = explode('/', $url);

        $user = User::where('uuid', $uuid[count($uuid) - 1])->firstOrFail();

        return [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'second_last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'telephone' => ['required', 'string', 'max:20'],
            'partner' => ['string', 'exists:users,uuid'],
            'type' => ['required', 'integer', 'max:3'],
        ];
    }
}
