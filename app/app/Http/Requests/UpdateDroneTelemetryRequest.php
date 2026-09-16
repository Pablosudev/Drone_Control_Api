<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDroneTelemetryRequest extends FormRequest
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
            'battery_percentage' => ['required','integer','between:0,100'],
            'status' => ['required','string','in:available,flying,charging,offline'],
            'latitude' => ['present','nullable','numeric','between:-90,90','required_with:longitude'],
            'longitude' => ['present','nullable','numeric','between:-180,180','required_with:latitude'],
            'sequence' => ['required','integer','min:0'],
            'observed_at' => ['required','date'],
            'message_id' => ['required','uuid']
        ];
    }
}
