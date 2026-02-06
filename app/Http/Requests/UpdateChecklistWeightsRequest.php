<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChecklistWeightsRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'zone_fresh_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'zone_original_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'zone_flip_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'zone_lol_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'zone_min_rr_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'zone_big_brother_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'technical_very_exp_chp_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'technical_exp_chp_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'fundamental_valuation_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'fundamental_seasonal_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'fundamental_noncommercial_divergence_weight' => ['required', 'integer', 'min:0', 'max:100'],
            'fundamental_cot_index_weight' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            '*.required' => 'All weight fields are required.',
            '*.integer' => 'Weight values must be whole numbers.',
            '*.min' => 'Weight values must be at least 0.',
            '*.max' => 'Weight values cannot exceed 100.',
        ];
    }
}
