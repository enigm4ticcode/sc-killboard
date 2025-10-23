<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateKillApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'exists:users,username'],
            'timestamp' => ['required', 'string', 'date_format:Y-m-d\TH:i:s.u\Z'],
            'kill_type' => ['required', 'string', 'min:2', Rule::in(['vehicle', 'fps'])],
            'location' => ['required', 'string', 'min:4'],
            'killer' => ['required', 'string', 'min:3'],
            'victim' => ['required', 'string', 'min:3'],
            'vehicle' => ['required', 'string', 'min:4'],
            'weapon' => ['sometimes', 'string', 'min:3'],
        ];
    }
}
