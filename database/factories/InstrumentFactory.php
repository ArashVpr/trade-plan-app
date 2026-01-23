<?php

namespace Database\Factories;

use App\Models\Instrument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Instrument>
 */
class InstrumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Instrument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $forexPairs = [
            ['symbol' => 'EURUSD', 'name' => 'Euro / US Dollar'],
            ['symbol' => 'GBPUSD', 'name' => 'British Pound / US Dollar'],
            ['symbol' => 'USDJPY', 'name' => 'US Dollar / Japanese Yen'],
            ['symbol' => 'AUDUSD', 'name' => 'Australian Dollar / US Dollar'],
            ['symbol' => 'USDCAD', 'name' => 'US Dollar / Canadian Dollar'],
            ['symbol' => 'NZDUSD', 'name' => 'New Zealand Dollar / US Dollar'],
            ['symbol' => 'EURGBP', 'name' => 'Euro / British Pound'],
            ['symbol' => 'EURJPY', 'name' => 'Euro / Japanese Yen'],
        ];

        $pair = $this->faker->randomElement($forexPairs);

        return [
            'symbol' => $pair['symbol'],
            'name' => $pair['name'],
            'type' => 'forex',
            'category' => $this->faker->randomElement(['major', 'minor']),
            'exchange' => 'FX',
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the instrument is inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * Create a commodity instrument.
     */
    public function commodity()
    {
        $commodities = [
            ['symbol' => 'XAUUSD', 'name' => 'Gold / US Dollar'],
            ['symbol' => 'XAGUSD', 'name' => 'Silver / US Dollar'],
            ['symbol' => 'CL', 'name' => 'Crude Oil'],
            ['symbol' => 'NG', 'name' => 'Natural Gas'],
        ];

        $commodity = $this->faker->randomElement($commodities);

        return $this->state(function (array $attributes) use ($commodity) {
            return [
                'symbol' => $commodity['symbol'],
                'name' => $commodity['name'],
                'type' => 'commodity',
                'category' => 'energy',
                'exchange' => 'NYMEX',
            ];
        });
    }

    /**
     * Create an index instrument.
     */
    public function index()
    {
        $indices = [
            ['symbol' => 'SPX', 'name' => 'S&P 500 Index'],
            ['symbol' => 'IXIC', 'name' => 'NASDAQ Composite'],
            ['symbol' => 'DJI', 'name' => 'Dow Jones Industrial Average'],
            ['symbol' => 'FTSE', 'name' => 'FTSE 100 Index'],
        ];

        $index = $this->faker->randomElement($indices);

        return $this->state(function (array $attributes) use ($index) {
            return [
                'symbol' => $index['symbol'],
                'name' => $index['name'],
                'type' => 'index',
                'category' => 'top100',
                'exchange' => 'INDEX',
            ];
        });
    }
}
