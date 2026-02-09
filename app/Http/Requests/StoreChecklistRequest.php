<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChecklistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare data for validation - support both 'screenshot' (singular) and 'screenshots' (plural)
     */
    protected function prepareForValidation(): void
    {
        // If screenshot (singular) is provided but screenshots (plural) is not, convert it
        if ($this->has('screenshot') && ! $this->has('screenshots')) {
            $this->merge([
                'screenshots' => [$this->input('screenshot')],
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'zone_qualifiers' => ['array'],
            'technicals' => ['array'],
            'technicals.location' => ['nullable', 'in:Very Cheap,Cheap,EQ,Expensive,Very Expensive'],
            'technicals.direction' => ['nullable', 'in:Impulsion,Correction'],
            'fundamentals' => ['array'],
            'fundamentals.valuation' => ['nullable', 'in:Undervalued,Overvalued,Fairly Valued,Neutral,Not Assessed'],
            'fundamentals.seasonalConfluence' => ['nullable', 'in:Bullish,Bearish,Neutral'],
            'fundamentals.nonCommercials' => ['nullable', 'in:Bullish Divergence,Bearish Divergence,No Divergence,Neutral,Not Assessed'],
            'fundamentals.cotIndex' => ['nullable', 'in:Bullish,Bearish,Neutral,Not Assessed'],
            'exclude_fundamentals' => ['boolean'],
            'score' => ['integer', 'min:0', 'max:100'],
            'symbol' => ['nullable', 'string', 'max:255'],
            // Order entry - now optional
            'entry_date' => ['nullable', 'date'],
            'position_type' => ['nullable', 'in:Long,Short'],
            'entry_price' => ['nullable', 'numeric'],
            'stop_price' => ['nullable', 'numeric'],
            'target_price' => ['nullable', 'numeric'],
            'trade_status' => ['nullable', 'in:pending,active,win,loss,breakeven,cancelled'],
            'rrr' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
            'screenshots' => ['nullable', 'array', 'max:5'],
            'screenshots.*' => ['file', 'image', 'max:5120'], // Max 5MB per image
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
            'technicals.required' => 'Please complete the technical analysis step.',
            'fundamentals.required' => 'Please complete the fundamental analysis step.',
            'score.integer' => 'The evaluation score must be a number.',
            'score.min' => 'The evaluation score must be at least 0.',
            'score.max' => 'The evaluation score cannot exceed 100.',
            'position_type.in' => 'Position type must be either Long or Short.',
            'screenshots.max' => 'You can upload a maximum of 5 screenshots.',
            'screenshots.*.max' => 'Each screenshot must not exceed 5MB.',
        ];
    }
}
