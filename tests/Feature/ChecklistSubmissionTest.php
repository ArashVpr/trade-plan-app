<?php

use App\Models\Checklist;
use App\Models\ChecklistWeights;
use App\Models\Instrument;
use App\Models\TradeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create authenticated user for all tests
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Create default checklist weights for the user
    $this->weights = ChecklistWeights::factory()->create([
        'user_id' => $this->user->id,
    ]);

    // Create unique active instruments for symbol selection
    $this->instruments = collect([
        Instrument::factory()->create(['symbol' => 'EURUSD', 'name' => 'Euro / US Dollar', 'is_active' => true]),
        Instrument::factory()->create(['symbol' => 'GBPUSD', 'name' => 'British Pound / US Dollar', 'is_active' => true]),
        Instrument::factory()->create(['symbol' => 'USDJPY', 'name' => 'US Dollar / Japanese Yen', 'is_active' => true]),
    ]);
});

// Helper function to create valid analysis data
function validAnalysisData(array $overrides = []): array
{
    return array_merge([
        'zone_qualifiers' => ['Fresh', 'Original'],
        'technicals' => [
            'location' => 'Cheap',
            'direction' => 'Impulsion',
        ],
        'fundamentals' => [
            'valuation' => 'Undervalued',
            'seasonalConfluence' => 'Bullish',
            'nonCommercials' => 'Bullish Divergence',
            'cotIndex' => 'Bullish',
        ],
        'exclude_fundamentals' => false,
        'score' => 75,
        'symbol' => 'EURUSD',
    ], $overrides);
}

// Helper function to create valid order entry data
function validOrderEntryData(array $overrides = []): array
{
    return array_merge([
        'entry_date' => '2026-01-22',
        'position_type' => 'Long',
        'entry_price' => 1.1000,
        'stop_price' => 1.0950,
        'target_price' => 1.1100,
        'rrr' => 2.0,
        'trade_status' => 'pending',
        'notes' => 'Test trade notes',
    ], $overrides);
}

// ============================================================================
// ANALYSIS-ONLY CHECKLIST TESTS (No Trade Entry)
// ============================================================================

test('can create analysis-only checklist with minimal valid data', function () {
    $data = validAnalysisData();

    $response = $this->post(route('checklists.store'), $data);

    $response->assertRedirect(route('checklists.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('checklists', [
        'user_id' => $this->user->id,
        'score' => 75,
        'symbol' => 'EURUSD',
    ]);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->zone_qualifiers)->toBe(['Fresh', 'Original']);
    expect($checklist->technicals)->toBe(['location' => 'Cheap', 'direction' => 'Impulsion']);
    expect($checklist->tradeEntry)->toBeNull();
});

test('can create checklist with all zone qualifiers selected', function () {
    $data = validAnalysisData([
        'zone_qualifiers' => [
            'Fresh',
            'Original',
            'Flip',
            'LOL',
            'Minimum 1:2 Profit Margin',
            'Big Brother Coverage',
        ],
    ]);

    $response = $this->post(route('checklists.store'), $data);

    $response->assertRedirect(route('checklists.index'));

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->zone_qualifiers)->toHaveCount(6);
});

test('checklist score is stored correctly', function () {
    $data = validAnalysisData(['score' => 85]);

    $this->post(route('checklists.store'), $data);

    $this->assertDatabaseHas('checklists', [
        'user_id' => $this->user->id,
        'score' => 85,
    ]);
});

test('can create checklist with valid symbol from instruments', function () {
    $instrument = $this->instruments->first();

    $data = validAnalysisData(['symbol' => $instrument->symbol]);

    $this->post(route('checklists.store'), $data);

    $this->assertDatabaseHas('checklists', [
        'symbol' => $instrument->symbol,
    ]);
});

test('can create checklist without symbol (nullable)', function () {
    $data = validAnalysisData(['symbol' => null]);

    $response = $this->post(route('checklists.store'), $data);

    $response->assertRedirect(route('checklists.index'));

    $this->assertDatabaseHas('checklists', [
        'user_id' => $this->user->id,
        'symbol' => null,
    ]);
});

test('validates score is within 0-100 range', function () {
    $data = validAnalysisData(['score' => 150]);

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('score');
});

test('validates score minimum is 0', function () {
    $data = validAnalysisData(['score' => -10]);

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('score');
});

// ============================================================================
// FULL ORDER ENTRY TESTS (Creates TradeEntry)
// ============================================================================

test('creates trade entry when all 5 required order fields are provided', function () {
    $data = array_merge(validAnalysisData(), validOrderEntryData());

    $response = $this->post(route('checklists.store'), $data);

    $response->assertRedirect(route('checklists.index'));

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry)->not()->toBeNull();

    $this->assertDatabaseHas('trade_entries', [
        'checklist_id' => $checklist->id,
        'user_id' => $this->user->id,
        'position_type' => 'Long',
        'entry_price' => 1.1000,
        'stop_price' => 1.0950,
        'target_price' => 1.1100,
        'trade_status' => 'pending',
    ]);
});

test('creates trade entry with pending status by default', function () {
    $orderData = validOrderEntryData();
    unset($orderData['trade_status']); // Remove explicit status

    $data = array_merge(validAnalysisData(), $orderData);

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry->trade_status)->toBe('pending');
});

test('creates trade entry with active status', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['trade_status' => 'active'])
    );

    $this->post(route('checklists.store'), $data);

    $this->assertDatabaseHas('trade_entries', [
        'user_id' => $this->user->id,
        'trade_status' => 'active',
    ]);
});

test('creates trade entry with win status', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['trade_status' => 'win'])
    );

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry->isWin())->toBeTrue();
});

test('creates trade entry with loss status', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['trade_status' => 'loss'])
    );

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry->isLoss())->toBeTrue();
});

test('creates trade entry with breakeven status', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['trade_status' => 'breakeven'])
    );

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry->isBreakeven())->toBeTrue();
});

test('creates trade entry with cancelled status', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['trade_status' => 'cancelled'])
    );

    $this->post(route('checklists.store'), $data);

    $this->assertDatabaseHas('trade_entries', [
        'user_id' => $this->user->id,
        'trade_status' => 'cancelled',
    ]);
});

test('creates long position trade entry', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['position_type' => 'Long'])
    );

    $this->post(route('checklists.store'), $data);

    $this->assertDatabaseHas('trade_entries', [
        'position_type' => 'Long',
    ]);
});

test('creates short position trade entry', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['position_type' => 'Short'])
    );

    $this->post(route('checklists.store'), $data);

    $this->assertDatabaseHas('trade_entries', [
        'position_type' => 'Short',
    ]);
});

// ============================================================================
// VALIDATION AND EDGE CASE TESTS
// ============================================================================

test('creates trade entry when partial order data provided (missing entry_price)', function () {
    $partialOrder = validOrderEntryData();
    unset($partialOrder['entry_price']); // Missing field - but TradeEntry still created with partial data

    $data = array_merge(validAnalysisData(), $partialOrder);

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry)->not()->toBeNull();
});

test('creates trade entry when partial order data provided (missing stop_price)', function () {
    $partialOrder = validOrderEntryData();
    unset($partialOrder['stop_price']);

    $data = array_merge(validAnalysisData(), $partialOrder);

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry)->not()->toBeNull();
});

test('creates trade entry when partial order data provided (missing target_price)', function () {
    $partialOrder = validOrderEntryData();
    unset($partialOrder['target_price']);

    $data = array_merge(validAnalysisData(), $partialOrder);

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry)->not()->toBeNull();
});

test('creates trade entry when partial order data provided (missing entry_date)', function () {
    $partialOrder = validOrderEntryData();
    unset($partialOrder['entry_date']);

    $data = array_merge(validAnalysisData(), $partialOrder);

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry)->not()->toBeNull();
});

test('creates trade entry when partial order data provided (missing position_type)', function () {
    $partialOrder = validOrderEntryData();
    unset($partialOrder['position_type']);

    $data = array_merge(validAnalysisData(), $partialOrder);

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry)->not()->toBeNull();
});

test('validates position_type must be Long or Short', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['position_type' => 'Invalid'])
    );

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('position_type');
});

test('validates trade_status must be valid enum value', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['trade_status' => 'invalid_status'])
    );

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('trade_status');
});

test('validates entry_date must be valid date format', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['entry_date' => 'not-a-date'])
    );

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('entry_date');
});

test('validates entry_price must be numeric', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['entry_price' => 'not-numeric'])
    );

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('entry_price');
});

test('validates stop_price must be numeric', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['stop_price' => 'not-numeric'])
    );

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('stop_price');
});

test('validates target_price must be numeric', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['target_price' => 'not-numeric'])
    );

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('target_price');
});

test('validates rrr must be numeric when provided', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['rrr' => 'not-numeric'])
    );

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('rrr');
});

test('validates zone_qualifiers must be array', function () {
    $data = validAnalysisData(['zone_qualifiers' => 'not-an-array']);

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('zone_qualifiers');
});

test('validates technicals must be array', function () {
    $data = validAnalysisData(['technicals' => 'not-an-array']);

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('technicals');
});

test('validates fundamentals must be array', function () {
    $data = validAnalysisData(['fundamentals' => 'not-an-array']);

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('fundamentals');
});

// ============================================================================
// AUTHORIZATION AND TRANSACTION TESTS
// ============================================================================

test('prevents unauthorized user from updating another users checklist', function () {
    // Create checklist as first user
    $originalUser = $this->user;
    $checklist = Checklist::factory()->create(['user_id' => $originalUser->id]);

    // Login as different user
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);

    $updateData = validAnalysisData(['score' => 90]);

    $response = $this->put(route('checklists.update', $checklist), $updateData);

    $response->assertForbidden();
});

test('allows user to update their own checklist', function () {
    $checklist = Checklist::factory()->create(['user_id' => $this->user->id]);

    $updateData = array_merge(validAnalysisData(['score' => 90]), [
        'technicals' => [
            'location' => 'Very Expensive',
            'direction' => 'Correction',
        ],
    ]);

    $response = $this->put(route('checklists.update', $checklist), $updateData);

    $response->assertRedirect(route('checklists.show', $checklist));

    $this->assertDatabaseHas('checklists', [
        'id' => $checklist->id,
        'score' => 90,
    ]);
});

test('cascade deletes trade entry when checklist is deleted', function () {
    $data = array_merge(validAnalysisData(), validOrderEntryData());
    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    $tradeEntryId = $checklist->tradeEntry->id;

    // Delete checklist
    $this->post(route('checklists.destroy', $checklist));

    // Verify both are deleted
    $this->assertDatabaseMissing('checklists', ['id' => $checklist->id]);
    $this->assertDatabaseMissing('trade_entries', ['id' => $tradeEntryId]);
});

test('can view own checklist', function () {
    $checklist = Checklist::factory()->create(['user_id' => $this->user->id]);

    $response = $this->get(route('checklists.show', $checklist));

    $response->assertOk();
});

test('can access checklist edit page for own checklist', function () {
    $checklist = Checklist::factory()->create(['user_id' => $this->user->id]);

    $response = $this->get(route('checklists.edit', $checklist));

    $response->assertOk();
});

// ============================================================================
// ADDITIONAL EDGE CASES
// ============================================================================

test('stores notes in trade entry when provided', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['notes' => 'This is a test trade with detailed notes'])
    );

    $this->post(route('checklists.store'), $data);

    $this->assertDatabaseHas('trade_entries', [
        'notes' => 'This is a test trade with detailed notes',
    ]);
});

test('stores rrr in trade entry when provided', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(['rrr' => 3.5])
    );

    $this->post(route('checklists.store'), $data);

    $this->assertDatabaseHas('trade_entries', [
        'rrr' => 3.5,
    ]);
});

test('accepts null rrr in trade entry', function () {
    $orderData = validOrderEntryData();
    unset($orderData['rrr']);

    $data = array_merge(validAnalysisData(), $orderData);

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry->rrr)->toBeNull();
});

test('accepts null notes in trade entry', function () {
    $orderData = validOrderEntryData();
    unset($orderData['notes']);

    $data = array_merge(validAnalysisData(), $orderData);

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    expect($checklist->tradeEntry->notes)->toBeNull();
});

test('handles decimal precision for prices correctly', function () {
    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData([
            'entry_price' => 1.12345678,
            'stop_price' => 1.12005678,
            'target_price' => 1.12685678,
        ])
    );

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();

    // SQLite doesn't enforce decimal precision like MySQL (stores full precision)
    // Test that values are stored correctly (MySQL would round to 4 decimals)
    expect((float) $checklist->tradeEntry->entry_price)->toBe(1.12345678);
    expect((float) $checklist->tradeEntry->stop_price)->toBe(1.12005678);
    expect((float) $checklist->tradeEntry->target_price)->toBe(1.12685678);
});

test('can update checklist analysis without affecting existing trade entry', function () {
    // Create checklist with trade entry
    $data = array_merge(validAnalysisData(), validOrderEntryData());
    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    $originalTradeEntry = $checklist->tradeEntry;

    // Update only analysis data
    $updateData = validAnalysisData([
        'score' => 95,
        'technicals' => [
            'location' => 'Very Cheap',
            'direction' => 'Impulsion',
        ],
    ]);

    $this->put(route('checklists.update', $checklist), $updateData);

    $checklist->refresh();

    expect($checklist->score)->toBe(95);
    expect($checklist->tradeEntry->id)->toBe($originalTradeEntry->id);
    expect($checklist->tradeEntry->entry_price)->toBe($originalTradeEntry->entry_price);
});

test('can update trade entry details along with checklist', function () {
    // Create checklist with trade entry
    $data = array_merge(validAnalysisData(), validOrderEntryData());
    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();

    // Update both checklist and trade entry
    $updateData = array_merge(
        validAnalysisData(['score' => 88]),
        validOrderEntryData([
            'trade_status' => 'active',
            'notes' => 'Updated notes after trade activation',
        ]),
        [
            'technicals' => [
                'location' => 'Expensive',
                'direction' => 'Correction',
            ],
        ]
    );

    $this->put(route('checklists.update', $checklist), $updateData);

    $checklist->refresh();

    expect($checklist->score)->toBe(88);
    expect($checklist->tradeEntry->trade_status)->toBe('active');
    expect($checklist->tradeEntry->notes)->toBe('Updated notes after trade activation');
});

test('symbol cannot be changed during update', function () {
    $checklist = Checklist::factory()->create([
        'user_id' => $this->user->id,
        'symbol' => 'EURUSD',
    ]);

    $updateData = array_merge(validAnalysisData(['symbol' => 'GBPUSD']), [
        'technicals' => [
            'location' => 'Cheap',
            'direction' => 'Impulsion',
        ],
    ]);

    $this->put(route('checklists.update', $checklist), $updateData);

    $checklist->refresh();

    // Symbol should remain unchanged
    expect($checklist->symbol)->toBe('EURUSD');
});

test('validates technicals location must be valid enum in update', function () {
    $checklist = Checklist::factory()->create(['user_id' => $this->user->id]);

    $updateData = array_merge(validAnalysisData(), [
        'technicals' => [
            'location' => 'Invalid Location',
            'direction' => 'Impulsion',
        ],
    ]);

    $response = $this->put(route('checklists.update', $checklist), $updateData);

    $response->assertSessionHasErrors('technicals.location');
});

test('validates technicals direction must be valid enum in update', function () {
    $checklist = Checklist::factory()->create(['user_id' => $this->user->id]);

    $updateData = array_merge(validAnalysisData(), [
        'technicals' => [
            'location' => 'Cheap',
            'direction' => 'Invalid Direction',
        ],
    ]);

    $response = $this->put(route('checklists.update', $checklist), $updateData);

    $response->assertSessionHasErrors('technicals.direction');
});

test('validates fundamentals valuation must be valid enum in update', function () {
    $checklist = Checklist::factory()->create(['user_id' => $this->user->id]);

    $updateData = array_merge(validAnalysisData(), [
        'fundamentals' => [
            'valuation' => 'Invalid',
            'seasonalConfluence' => 'Bullish',
            'nonCommercials' => 'Bullish Divergence',
            'cotIndex' => 'Bullish',
        ],
    ]);

    $response = $this->put(route('checklists.update', $checklist), $updateData);

    $response->assertSessionHasErrors('fundamentals.valuation');
});

test('allows partial fundamental fields in update', function () {
    $checklist = Checklist::factory()->create(['user_id' => $this->user->id]);

    $updateData = array_merge(validAnalysisData(), [
        'fundamentals' => [
            'valuation' => 'Undervalued',
            // Other fields are optional
        ],
    ]);

    $response = $this->put(route('checklists.update', $checklist), $updateData);

    $response->assertRedirect();
});

test('allows partial technical fields in update', function () {
    $checklist = Checklist::factory()->create(['user_id' => $this->user->id]);

    $updateData = array_merge(validAnalysisData(), [
        'technicals' => [
            'location' => 'Cheap',
            // direction is optional
        ],
    ]);

    $response = $this->put(route('checklists.update', $checklist), $updateData);

    $response->assertRedirect();
});

// ============================================================================
// FILE UPLOAD TESTS
// ============================================================================

test('stores screenshot file when provided with trade entry', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('trade-setup.png', 800, 600);

    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(),
        ['screenshot' => $file]
    );

    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();

    // Verify file was stored
    expect($checklist->tradeEntry->screenshot_path)->not()->toBeNull();
    Storage::disk('public')->assertExists($checklist->tradeEntry->screenshot_path);
});

test('validates screenshot must be an image file', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('document.pdf', 100);

    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(),
        ['screenshot' => $file]
    );

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('screenshots.0');
});

test('validates screenshot file size does not exceed 10MB', function () {
    Storage::fake('public');

    // Create 11MB file
    $file = UploadedFile::fake()->image('huge-screenshot.png')->size(11000);

    $data = array_merge(
        validAnalysisData(),
        validOrderEntryData(),
        ['screenshot' => $file]
    );

    $response = $this->post(route('checklists.store'), $data);

    $response->assertSessionHasErrors('screenshots.0');
});

test('can update screenshot file in existing trade entry', function () {
    Storage::fake('public');

    // Create checklist with trade entry and screenshot
    $originalFile = UploadedFile::fake()->image('original.png');
    $data = array_merge(validAnalysisData(), validOrderEntryData(), ['screenshot' => $originalFile]);
    $this->post(route('checklists.store'), $data);

    $checklist = Checklist::where('user_id', $this->user->id)->first();
    $originalPath = $checklist->tradeEntry->screenshot_path;

    // Update with new screenshot
    $newFile = UploadedFile::fake()->image('updated.png');
    $updateData = array_merge(
        validAnalysisData(),
        validOrderEntryData(),
        ['screenshot' => $newFile]
    );

    $this->put(route('checklists.update', $checklist), $updateData);

    $checklist->refresh();

    // Verify new file was stored and path updated
    expect($checklist->tradeEntry->screenshot_path)->not()->toBe($originalPath);
    Storage::disk('public')->assertExists($checklist->tradeEntry->screenshot_path);
});
