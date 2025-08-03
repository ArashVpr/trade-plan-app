# Trade Plan App - AI Agent Instructions

## Application Overview

This is a Laravel 12 + Inertia.js + Vue 3 trading analysis application where traders create scored checklists to evaluate trade setups before execution. Think of it as a systematic approach to trade evaluation with optional order entry tracking.

## Documentations

-   [Laravel Documentation](https://laravel.com/docs/12.x)
-   [Inertia.js Documentation](https://inertiajs.com/)
-   [Vue 3 Documentation](https://vuejs.org/guide/introduction.html)
-   [PrimeVue Documentation](https://primevue.org/)

## Core Architecture Pattern

### Data Flow: Checklist → TradeEntry (Optional)

-   **Checklist**: Analysis-only evaluation with scoring (always created)
-   **TradeEntry**: Optional order execution details (created when trader decides to execute)
-   Relationship: `Checklist hasOne TradeEntry` (not always present)

### Key Models & Relationships

```php
// Core domain models
Checklist: user_id, zone_qualifiers[], technicals{}, fundamentals{}, score, symbol
TradeEntry: checklist_id, entry_date, position_type, entry_price, stop_price, target_price, trade_status, rrr
ChecklistWeights: Customizable scoring weights for checklist evaluation
```

### Frontend Stack

-   **Inertia.js** for SPA experience with Laravel backend
-   **PrimeVue 4** components with auto-import via `unplugin-vue-components`
-   **TailwindCSS 4** for styling
-   **Chart.js + vue-chartjs** for dashboard analytics
-   Custom theme: `TradingTheme` extends PrimeVue Aura preset

## Critical Patterns

### Scoring System

Checklists use weighted scoring based on `ChecklistWeights`. The `EvaluationScore.vue` component calculates scores using these weights:

-   Zone qualifiers (6 types): `zone_*_weight`
-   Technical analysis: `technical_*_weight`
-   Fundamental analysis: `fundamental_*_weight`

### Authentication Placeholder

Currently uses hardcoded `user_id = 1` throughout controllers. Search for `// Replace with auth()->id()` comments when implementing real auth.

### Factory States & Realistic Data

Factories create realistic trading scenarios:

```php
Checklist::factory()->withPendingOrder()->create()    // Has TradeEntry with status 'pending'
Checklist::factory()->analysisOnly()->create()        // No TradeEntry (analysis only)
```

### Component Auto-Import

PrimeVue components auto-import via `PrimeVueResolver`. Custom components in `resources/js/Components/` also auto-import. Check `components.d.ts` for available globals.

## Development Workflows

### Database Operations

```bash
php artisan migrate:fresh --seed    # Reset with realistic test data
php artisan db:seed --class=ChecklistSeeder    # Add more sample data
```

### Frontend Development

```bash
npm run dev    # Vite development server with HMR
npm run build  # Production build
```

### Testing with Pest

```bash
php artisan test    # Run Pest test suite
```

## Project-Specific Conventions

### Form Handling Pattern

-   Use `useForm()` from Inertia for reactive forms
-   Validation errors accessible via `$errors` computed property
-   Submit with `router.get()` or `form.post()` for navigation

### Vue Component Structure

-   Step components in `ChecklistSteps/` follow `@update:model-value` pattern
-   UI components in `Components/UI/` are reusable primitives
-   Pages use `AppLayout.vue` wrapper with drawer navigation

### Trade Status Mapping

TradeEntry `trade_status` enum maps to display labels:

```php
'pending' → 'Pending', 'active' → 'Open', 'win' → 'Win', 'loss' → 'Loss'
```

### Route Naming

-   Resource routes: `checklists.index`, `checklists.show`, `checklists.edit`
-   Special routes: `home` (checklist wizard), `dashboard`, `checklist-weights.index`

### Styling Conventions

-   Primary color: `text-blue-900` for headers and branding
-   Score severity: `danger` (<50), `warn` (50-80), `success` (80+)
-   Card-based layout with PrimeVue `Card` component

## Integration Points

### Ziggy Route Generation

Laravel routes available in Vue via `route()` function from `ziggy-js`. Used extensively for navigation.

### Chart.js Integration

Dashboard uses computed properties to transform Laravel controller data into Chart.js format. See `weeklyTrendData` and `scoreDistributionData` in `Dashboard.vue`.

### File Uploads

Optional screenshot uploads in `OrderEntryStep.vue` using PrimeVue `FileUpload` component.

## Key Files to Reference

-   `app/Http/Controllers/ChecklistController.php` - Core CRUD operations
-   `resources/js/Pages/ChecklistWizard.vue` - Multi-step form pattern
-   `resources/js/Components/UI/EvaluationScore.vue` - Scoring calculation logic
-   `database/factories/ChecklistFactory.php` - Realistic test data patterns
-   `resources/js/app.js` - Frontend bootstrap and theming setup
