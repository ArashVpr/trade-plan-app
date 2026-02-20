<template>
    <div class="inline-flex items-center" :title="symbol">
        <template v-if="isForex && flagLocalUrls.length">
            <div class="flags-wrapper">
                <template v-for="(localUrl, i) in flagLocalUrls" :key="i">
                    <img v-if="!failedForexLocal[i]" :src="localUrl" @error="onForexLocalError(i)"
                        :class="['flag', i === 1 ? 'flag-right' : '']" :alt="symbol" />
                    <img v-else-if="!failedForexRemote[i]" :src="flagRemoteUrls[i]" @error="onForexRemoteError(i)"
                        :class="['flag', i === 1 ? 'flag-right' : '']" :alt="symbol" />
                </template>
                <i v-if="allForexFailed" class="pi pi-chart-line text-gray-400"></i>
            </div>
        </template>

        <template v-else-if="isCryptoPair && cryptoPairLocalUrls.length">
            <div class="flags-wrapper">
                <template v-for="(localUrl, i) in cryptoPairLocalUrls" :key="`crypto-${i}`">
                    <img v-if="!failedCryptoLocal[i]" :src="localUrl" @error="onCryptoLocalError(i)"
                        :class="['flag', i === 1 ? 'flag-right' : '']" :alt="symbol" />
                    <img v-else-if="!failedCryptoRemote[i]" :src="cryptoPairRemoteUrls[i]"
                        @error="onCryptoRemoteError(i)" :class="['flag', i === 1 ? 'flag-right' : '']" :alt="symbol" />
                </template>
                <i v-if="allCryptoFailed" class="pi pi-chart-line text-gray-400"></i>
            </div>
        </template>

        <template v-else>
            <img v-if="currentUrl && !failed[currentIndex]" :src="currentUrl" @error="onSingleError" :class="imgClass"
                :alt="symbol" />
            <i v-else class="pi pi-chart-line text-gray-400"></i>
        </template>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
    symbol: { type: String, required: false },
    size: { type: String, default: 'small' }
})

const currencyToCountry = {
    USD: 'US', EUR: 'EU', GBP: 'GB', JPY: 'JP', AUD: 'AU', CAD: 'CA', CHF: 'CH', NZD: 'NZ'
}

const cryptoBaseToLocalLogo = {
    BTC: 'btc.svg', ETH: 'eth.svg', LTC: 'ltc.svg', XRP: 'xrp.svg', ADA: 'ada.svg', SOL: 'sol.svg',
    DOT: 'dot.svg', AVAX: 'avax.svg', TRX: 'trx.svg', BNB: 'bnb.svg', USDT: 'usdt.svg', USDC: 'usdc.svg', DOGE: 'doge.svg'
}

const cryptoQuoteCandidates = ['USDT', 'USDC', 'USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD', 'CHF', 'NZD']

const stockTickerToSlug = {
    AAPL: 'apple', MSFT: 'microsoft', GOOGL: 'alphabet', AMZN: 'amazon', TSLA: 'tesla', NVDA: 'nvidia', META: 'meta-platforms',
    NFLX: 'netflix', JPM: 'jpmorgan', V: 'visa', MA: 'mastercard', WMT: 'walmart', DIS: 'disney', ORCL: 'oracle',
    CRM: 'salesforce', AMD: 'amd', INTC: 'intel', BAC: 'bank-of-america', T: 'at-t', RACE: 'ferrari',
    BABA: 'alibaba', UBER: 'uber', SPOT: 'spotify-technology', PYPL: 'paypal', ZOOM: 'zoom-video-communications', SHOP: 'shopify', XYZ: 'block', COIN: 'coinbase',
    IBM: 'international-bus-mach'
}

const symbolToLocalLogo = {
    // indices
    USDX: 'indices/u-s-dollar-index--big.svg', DXY: 'indices/u-s-dollar-index--big.svg',
    DJI: 'indices/dow-30.svg', SPX: 'indices/s-and-p-500.svg', NDX: 'indices/nasdaq-100.svg',
    RUT: 'indices/russell-2000.svg',
    // non-US indices -> use country flags
    FTSE: 'country/GB--big.svg', DAX: 'country/DE.svg', CAC: 'country/FR.svg',
    NIKKEI: 'country/JP--big.svg', ASX200: 'country/AU--big.svg', IBEX: 'country/ES.svg', EU50: 'country/EU--big.svg',

    // metals
    XAUUSD: 'metal/gold--big.svg', XAGUSD: 'metal/silver--big.svg', XPTUSD: 'metal/platinum--big.svg',
    XPDUSD: 'metal/palladium--big.svg', XCUUSD: 'metal/copper--big.svg', COPPER: 'metal/copper--big.svg',

    // energy
    USOIL: 'energy/crude-oil--big.svg', UKOIL: 'energy/crude-oil--big.svg', WTI: 'energy/crude-oil--big.svg',
    NGAS: 'energy/natural-gas--big.svg', HEAT: 'energy/diesel.svg',

    // agricultural
    CORN: 'agricultural/corn.svg', WHEAT: 'agricultural/wheat.svg', SOYBEAN: 'agricultural/soybeans.svg',
    COCOA: 'agricultural/cocoa.svg', COFFEE: 'agricultural/coffee.svg', SUGAR: 'agricultural/sugar.svg',
    COTTON: 'agricultural/cotton.svg', RICE: 'agricultural/rough-rice.svg',

    // crypto symbols
    BTC: 'crypto/btc.svg', ETH: 'crypto/eth.svg', XRP: 'crypto/xrp.svg', BNB: 'crypto/bnb.svg', SOL: 'crypto/sol.svg',
    USDT: 'crypto/usdt.svg', USDC: 'crypto/usdc.svg', DOGE: 'crypto/doge.svg', ADA: 'crypto/ada.svg', TRX: 'crypto/trx.svg'
}

const normalize = (s) => (s || '').toString().toUpperCase().replace(/[^A-Z0-9]/g, '')
const normalizedSymbol = computed(() => normalize(props.symbol))

const cryptoPairInfo = computed(() => {
    const s = normalizedSymbol.value
    if (!s || s.length < 6) {
        return null
    }

    const quote = cryptoQuoteCandidates.find((candidate) => s.endsWith(candidate))
    if (!quote) {
        return null
    }

    const base = s.slice(0, -quote.length)

    // Limit to known crypto bases to avoid matching commodity symbols like XAUUSD
    if (!Object.keys(cryptoBaseToLocalLogo).includes(base)) {
        return null
    }

    const effectiveQuote = quote === 'USD' ? 'USDT' : quote
    const quoteAsCrypto = cryptoBaseToLocalLogo[effectiveQuote]

    return {
        base,
        quote,
        effectiveQuote,
        localCoinFile: cryptoBaseToLocalLogo[base],
        quoteKind: quoteAsCrypto ? 'crypto' : 'country',
        quoteLocalFile: quoteAsCrypto || null,
        quoteCountry: quoteAsCrypto ? null : currencyToCountry[effectiveQuote]
    }
})

const isCryptoPair = computed(() => Boolean(cryptoPairInfo.value))

const isForex = computed(() => {
    if (!props.symbol) {
        return false
    }

    const s = normalizedSymbol.value
    if (!/^[A-Z]{6}$/.test(s)) {
        return false
    }

    const base = s.slice(0, 3)
    const quote = s.slice(3)

    // Treat as forex only when both legs are known currency codes.
    return Boolean(currencyToCountry[base]) && Boolean(currencyToCountry[quote])
})

const flagLocalUrls = computed(() => {
    if (!isForex.value) {
        return []
    }

    const base = normalizedSymbol.value.slice(0, 3)
    const quote = normalizedSymbol.value.slice(3)
    const baseCountry = currencyToCountry[base] || base.slice(0, 2)
    const quoteCountry = currencyToCountry[quote] || quote.slice(0, 2)

    return [
        `/tradingview-logos/country/${baseCountry}--big.svg`,
        `/tradingview-logos/country/${quoteCountry}--big.svg`
    ]
})

const flagRemoteUrls = computed(() => {
    if (!isForex.value) {
        return []
    }

    const base = normalizedSymbol.value.slice(0, 3)
    const quote = normalizedSymbol.value.slice(3)
    const baseCountry = currencyToCountry[base] || base.slice(0, 2)
    const quoteCountry = currencyToCountry[quote] || quote.slice(0, 2)

    return [
        `https://s3-symbol-logo.tradingview.com/country/${baseCountry}--big.svg`,
        `https://s3-symbol-logo.tradingview.com/country/${quoteCountry}--big.svg`
    ]
})

const cryptoPairLocalUrls = computed(() => {
    if (!isCryptoPair.value) {
        return []
    }

    const quoteUrl = cryptoPairInfo.value.quoteKind === 'crypto'
        ? `/tradingview-logos/crypto/${cryptoPairInfo.value.quoteLocalFile}`
        : `/tradingview-logos/country/${cryptoPairInfo.value.quoteCountry}--big.svg`

    return [
        `/tradingview-logos/crypto/${cryptoPairInfo.value.localCoinFile}`,
        quoteUrl
    ]
})

const cryptoPairRemoteUrls = computed(() => {
    if (!isCryptoPair.value) {
        return []
    }

    const quoteRemoteUrl = cryptoPairInfo.value.quoteKind === 'crypto'
        ? `https://s3-symbol-logo.tradingview.com/crypto/XTVC${cryptoPairInfo.value.effectiveQuote}.svg`
        : `https://s3-symbol-logo.tradingview.com/country/${cryptoPairInfo.value.quoteCountry}--big.svg`

    return [
        `https://s3-symbol-logo.tradingview.com/crypto/XTVC${cryptoPairInfo.value.base}.svg`,
        quoteRemoteUrl
    ]
})

const failedForexLocal = ref([])
const failedForexRemote = ref([])
const failedCryptoLocal = ref([])
const failedCryptoRemote = ref([])

const onForexLocalError = (index) => {
    failedForexLocal.value[index] = true
}

const onForexRemoteError = (index) => {
    failedForexRemote.value[index] = true
}

const onCryptoLocalError = (index) => {
    failedCryptoLocal.value[index] = true
}

const onCryptoRemoteError = (index) => {
    failedCryptoRemote.value[index] = true
}

const allForexFailed = computed(() => {
    if (!isForex.value) {
        return false
    }

    return flagLocalUrls.value.every((_, i) => Boolean(failedForexLocal.value[i]) && Boolean(failedForexRemote.value[i]))
})

const allCryptoFailed = computed(() => {
    if (!isCryptoPair.value) {
        return false
    }

    return cryptoPairLocalUrls.value.every((_, i) => Boolean(failedCryptoLocal.value[i]) && Boolean(failedCryptoRemote.value[i]))
})

const candidates = computed(() => {
    if (!props.symbol) {
        return []
    }

    const key = normalizedSymbol.value
    const list = []

    // 1) explicit symbol map to local file
    if (symbolToLocalLogo[key]) {
        list.push(`/tradingview-logos/${symbolToLocalLogo[key]}`)
        return list
    }

    // 2) stock ticker to local stock slug
    if (stockTickerToSlug[key]) {
        list.push(`/tradingview-logos/stocks/${stockTickerToSlug[key]}.svg`)
        return list
    }

    // 3) no known local mapping -> do not probe many URLs (prevents repeated 404 spam)
    return []
})

const failed = ref([])
const currentIndex = ref(0)

watch(
    () => props.symbol,
    () => {
        failed.value = []
        failedForexLocal.value = []
        failedForexRemote.value = []
        failedCryptoLocal.value = []
        failedCryptoRemote.value = []
        currentIndex.value = 0
    }
)

const currentUrl = computed(() => candidates.value[currentIndex.value] || null)

const onSingleError = () => {
    failed.value[currentIndex.value] = true

    if (currentIndex.value < candidates.value.length - 1) {
        currentIndex.value++
    }
}

const imgClass = computed(() => (props.size === 'small' ? 'w-6 h-6 rounded-sm' : 'w-8 h-8 rounded-sm'))
</script>

<style scoped>
.pi-chart-line {
    font-size: 1rem;
}

img {
    object-fit: cover;
}

.flags-wrapper {
    display: inline-flex;
    align-items: center;
    position: relative;
}

.flag {
    width: 18px;
    height: 18px;
    border-radius: 9999px;
    border: 1px solid #fff;
    box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.04);
    object-fit: cover;
}

.flag-right {
    margin-left: -6px;
}
</style>
