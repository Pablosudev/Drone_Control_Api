<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateDroneRequest extends FormRequest
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
            'name' => ['sometimes','string','max:255'],
            'serial_number' => ['sometimes','string','max:255', Rule::unique('drones', 'serial_number')->ignore($this->route('drone'))],'status' => ['missing'],
            'battery_percentage' => ['missing'],
            'latitude' => ['missing'],
            'longitude' => ['missing'],
        ];
    }
}
