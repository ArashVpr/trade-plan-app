<?php

namespace Database\Seeders;

use App\Models\Instrument;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    public function run(): void
    {
        $instruments = [
            // Forex Majors
            ['symbol' => 'EURUSD', 'name' => 'Euro vs US Dollar', 'type' => 'forex', 'category' => 'major'],
            ['symbol' => 'GBPUSD', 'name' => 'British Pound vs US Dollar', 'type' => 'forex', 'category' => 'major'],
            ['symbol' => 'USDJPY', 'name' => 'US Dollar vs Japanese Yen', 'type' => 'forex', 'category' => 'major'],
            ['symbol' => 'USDCHF', 'name' => 'US Dollar vs Swiss Franc', 'type' => 'forex', 'category' => 'major'],
            ['symbol' => 'AUDUSD', 'name' => 'Australian Dollar vs US Dollar', 'type' => 'forex', 'category' => 'major'],
            ['symbol' => 'USDCAD', 'name' => 'US Dollar vs Canadian Dollar', 'type' => 'forex', 'category' => 'major'],
            ['symbol' => 'NZDUSD', 'name' => 'New Zealand Dollar vs US Dollar', 'type' => 'forex', 'category' => 'major'],
            ['symbol' => 'USDX', 'name' => 'US Dollar Index', 'type' => 'forex', 'category' => 'index'],

            // Forex Minors
            ['symbol' => 'EURGBP', 'name' => 'Euro vs British Pound', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'EURJPY', 'name' => 'Euro vs Japanese Yen', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'EURAUD', 'name' => 'Euro vs Australian Dollar', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'EURCHF', 'name' => 'Euro vs Swiss Franc', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'EURCAD', 'name' => 'Euro vs Canadian Dollar', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'EURNZD', 'name' => 'Euro vs New Zealand Dollar', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'GBPJPY', 'name' => 'British Pound vs Japanese Yen', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'GBPAUD', 'name' => 'British Pound vs Australian Dollar', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'GBPCAD', 'name' => 'British Pound vs Canadian Dollar', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'GBPNZD', 'name' => 'British Pound vs New Zealand Dollar', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'GBPCHF', 'name' => 'British Pound vs Swiss Franc', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'AUDJPY', 'name' => 'Australian Dollar vs Japanese Yen', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'AUDNZD', 'name' => 'Australian Dollar vs New Zealand Dollar', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'AUDCHF', 'name' => 'Australian Dollar vs Swiss Franc', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'AUDCAD', 'name' => 'Australian Dollar vs Canadian Dollar', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'NZDJPY', 'name' => 'New Zealand Dollar vs Japanese Yen', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'NZDCHF', 'name' => 'New Zealand Dollar vs Swiss Franc', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'NZDCAD', 'name' => 'New Zealand Dollar vs Canadian Dollar', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'CADCHF', 'name' => 'Canadian Dollar vs Swiss Franc', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'CADJPY', 'name' => 'Canadian Dollar vs Japanese Yen', 'type' => 'forex', 'category' => 'minor'],
            ['symbol' => 'CHFJPY', 'name' => 'Swiss Franc vs Japanese Yen', 'type' => 'forex', 'category' => 'minor'],

            // Agricultural Commodities
            ['symbol' => 'CORN', 'name' => 'Corn', 'type' => 'commodity', 'category' => 'agricultural'],
            ['symbol' => 'WHEAT', 'name' => 'Wheat', 'type' => 'commodity', 'category' => 'agricultural'],
            ['symbol' => 'SOYBEAN', 'name' => 'Soybean', 'type' => 'commodity', 'category' => 'agricultural'],
            ['symbol' => 'COCOA', 'name' => 'Cocoa', 'type' => 'commodity', 'category' => 'agricultural'],
            ['symbol' => 'COFFEE', 'name' => 'Coffee', 'type' => 'commodity', 'category' => 'agricultural'],
            ['symbol' => 'SUGAR', 'name' => 'Sugar', 'type' => 'commodity', 'category' => 'agricultural'],
            ['symbol' => 'COTTON', 'name' => 'Cotton', 'type' => 'commodity', 'category' => 'agricultural'],
            ['symbol' => 'RICE', 'name' => 'Rice', 'type' => 'commodity', 'category' => 'agricultural'],


            // Metal Commodities
            ['symbol' => 'XAUUSD', 'name' => 'Gold vs US Dollar', 'type' => 'commodity', 'category' => 'metal'],
            ['symbol' => 'XAGUSD', 'name' => 'Silver vs US Dollar', 'type' => 'commodity', 'category' => 'metal'],
            ['symbol' => 'XCUUSD', 'name' => 'Copper vs US Dollar', 'type' => 'commodity', 'category' => 'metal'],
            ['symbol' => 'XPTUSD', 'name' => 'Platinum vs US Dollar', 'type' => 'commodity', 'category' => 'metal'],
            ['symbol' => 'XPDUSD', 'name' => 'Palladium vs US Dollar', 'type' => 'commodity', 'category' => 'metal'],

            // Energy Commodities
            ['symbol' => 'USOIL', 'name' => 'West Texas Intermediate Crude Oil', 'type' => 'commodity', 'category' => 'energy'],
            ['symbol' => 'UKOIL', 'name' => 'Brent Crude Oil', 'type' => 'commodity', 'category' => 'energy'],
            ['symbol' => 'NGAS', 'name' => 'Natural Gas', 'type' => 'commodity', 'category' => 'energy'],
            ['symbol' => 'HEAT', 'name' => 'Heating Oil', 'type' => 'commodity', 'category' => 'energy'],

            // Indices
            ['symbol' => 'DJI', 'name' => 'Dow Jones Industrial Average', 'type' => 'index', 'category' => null],
            ['symbol' => 'SPX', 'name' => 'S&P 500 Index', 'type' => 'index', 'category' => null],
            ['symbol' => 'NDX', 'name' => 'Nasdaq 100 Index', 'type' => 'index', 'category' => null],
            ['symbol' => 'RUT', 'name' => 'Russell 2000 Index', 'type' => 'index', 'category' => null],
            ['symbol' => 'FTSE', 'name' => 'FTSE 100 Index', 'type' => 'index', 'category' => null],
            ['symbol' => 'DAX', 'name' => 'DAX Index', 'type' => 'index', 'category' => null],
            ['symbol' => 'CAC', 'name' => 'CAC 40 Index', 'type' => 'index', 'category' => null],
            ['symbol' => 'NIKKEI', 'name' => 'Nikkei 225 Index', 'type' => 'index', 'category' => null],
            ['symbol' => 'ASX200', 'name' => 'S&P/ASX 200 Index', 'type' => 'index', 'category' => null],
            ['symbol' => 'IBEX', 'name' => 'IBEX 35 Index', 'type' => 'index', 'category' => null],
            ['symbol' => 'EU50', 'name' => 'Euro Stoxx 50 Index', 'type' => 'index', 'category' => null],
            


            // US Top 100 Stocks (sample - top 20 for brevity, can be expanded)
            ['symbol' => 'AAPL', 'name' => 'Apple Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'MSFT', 'name' => 'Microsoft Corporation', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'GOOGL', 'name' => 'Alphabet Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'AMZN', 'name' => 'Amazon.com Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'TSLA', 'name' => 'Tesla Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'NVDA', 'name' => 'NVIDIA Corporation', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'META', 'name' => 'Meta Platforms Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'NFLX', 'name' => 'Netflix Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'BABA', 'name' => 'Alibaba Group Holding Ltd.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'JPM', 'name' => 'JPMorgan Chase & Co.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'V', 'name' => 'Visa Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'MA', 'name' => 'Mastercard Incorporated', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'WMT', 'name' => 'Walmart Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'DIS', 'name' => 'The Walt Disney Company', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'ORCL', 'name' => 'Oracle Corporation', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'CRM', 'name' => 'Salesforce Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'AMD', 'name' => 'Advanced Micro Devices Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'INTC', 'name' => 'Intel Corporation', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'UBER', 'name' => 'Uber Technologies Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'SPOT', 'name' => 'Spotify Technology S.A.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'PYPL', 'name' => 'PayPal Holdings Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'ZOOM', 'name' => 'Zoom Video Communications Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'SHOP', 'name' => 'Shopify Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'SQ', 'name' => 'Block Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'COIN', 'name' => 'Coinbase Global Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'BAC', 'name' => 'Bank of America Corporation', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],
            ['symbol' => 'T', 'name' => 'AT&T Inc.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NASDAQ'],
            ['symbol' => 'RACE', 'name' => 'Ferrari N.V.', 'type' => 'stock', 'category' => 'top100', 'exchange' => 'NYSE'],

        ];

        foreach ($instruments as $instrument) {
            Instrument::create($instrument);
        }
    }
}
