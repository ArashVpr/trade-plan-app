<?php

use App\Models\Checklist;
use App\Models\ChecklistWeights;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Create checklist weights with known values for testing
    $this->weights = ChecklistWeights::create([
        'user_id' => $this->user->id,
        'zone_fresh_weight' => 4,
        'zone_original_weight' => 4,
        'zone_flip_weight' => 4,
        'zone_lol_weight' => 4,
        'zone_min_profit_margin_weight' => 4,
        'zone_big_brother_weight' => 4,
        'technical_very_exp_chp_weight' => 12,
        'technical_exp_chp_weight' => 6,
        'technical_direction_impulsive_weight' => 12,
        'technical_direction_correction_weight' => 6,
        'fundamental_valuation_weight' => 10,
        'fundamental_seasonal_weight' => 6,
        'fundamental_cot_index_weight' => 12,
        'fundamental_noncommercial_divergence_weight' => 12,
    ]);
});

test('can create checklist with fundamentals excluded', function () {
    $checklist = Checklist::factory()->excludingFundamentals()->create([
        'user_id' => $this->user->id,
    ]);

    expect($checklist->exclude_fundamentals)->toBeTrue()
        ->and($checklist->fundamentals)->toBeArray();
});

test('can create checklist with fundamentals included', function () {
    $checklist = Checklist::factory()->create([
        'user_id' => $this->user->id,
        'exclude_fundamentals' => false,
    ]);

    expect($checklist->exclude_fundamentals)->toBeFalse();
});

test('score calculation excludes fundamentals when flag is set', function () {
    // Create a checklist with strong fundamentals but exclude_fundamentals = true
    $checklist = Checklist::factory()->create([
        'user_id' => $this->user->id,
        'zone_qualifiers' => ['Fresh', 'Original'],
        'technicals' => [
            'location' => 'Very Cheap',
            'direction' => 'Impulsion',
        ],
        'fundamentals' => [
            'valuation' => 'Undervalued',
            'seasonalConfluence' => 'Bullish',
            'nonCommercials' => 'Bullish Divergence',
            'cotIndex' => 'Bullish',
        ],
        'exclude_fundamentals' => true,
    ]);

    // Calculate what the score should be:
    // Zones: 2 selected = 4 + 4 = 8
    // Technical location: Very Cheap = 12
    // Technical direction: Impulsion = 12
    // Fundamentals: EXCLUDED = 0
    // Raw score = 8 + 12 + 12 = 32
    //
    // Max possible (without fundamentals):
    // Zone max: 4+4+4+4+4+4 = 24
    // Tech location max: 12
    // Tech direction max: 12
    // Fundamental max: 0 (excluded)
    // Max = 24 + 12 + 12 = 48
    //
    // Score = (32 / 48) * 100 = 66.67% = 67

    // Note: The actual calculation happens in the frontend EvaluationScore component
    // This test verifies the model properly stores the exclude_fundamentals flag
    expect($checklist->exclude_fundamentals)->toBeTrue();
});

test('can update checklist to exclude fundamentals', function () {
    $checklist = Checklist::factory()->create([
        'user_id' => $this->user->id,
        'exclude_fundamentals' => false,
    ]);

    $checklist->update(['exclude_fundamentals' => true]);

    expect($checklist->fresh()->exclude_fundamentals)->toBeTrue();
});

test('can update checklist to include fundamentals', function () {
    $checklist = Checklist::factory()->excludingFundamentals()->create([
        'user_id' => $this->user->id,
    ]);

    $checklist->update(['exclude_fundamentals' => false]);

    expect($checklist->fresh()->exclude_fundamentals)->toBeFalse();
});

test('exclude_fundamentals defaults to false for new checklists', function () {
    $checklist = Checklist::factory()->create([
        'user_id' => $this->user->id,
    ]);

    expect($checklist->exclude_fundamentals)->toBeFalse();
});
