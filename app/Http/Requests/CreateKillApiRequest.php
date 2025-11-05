<?php

namespace App\Http\Requests;

use App\Models\Kill;
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
        $vehicleKillType = Kill::TYPE_VEHICLE;

        return [
            'username' => ['required', 'string', 'exists:users,username'],
            'timestamp' => ['required', 'string', 'date_format:Y-m-d\TH:i:s.u\Z'],
            'kill_type' => ['required', 'string', 'min:2', Rule::in([$vehicleKillType, Kill::TYPE_FPS])],
            'location' => ['required', 'string', 'min:4'],
            'killer' => ['required', 'string', 'min:3'],
            'victim' => ['required', 'string', 'min:3'],
            'weapon' => ['required', 'string', 'min:3'],
            'vehicle' => ['sometimes', "required_if:kill_type,$vehicleKillType", 'string', 'min:4'],
        ];
    }
}
