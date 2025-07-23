import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura'
import { definePreset } from '@primeuix/themes'
import 'primeicons/primeicons.css'

// Custom trading theme based on your app's colors
const TradingTheme = definePreset(Aura, {
  semantic: {
    primary: {
      50: '{blue.50}',
      100: '{blue.100}',
      200: '{blue.200}',
      300: '{blue.300}',
      400: '{blue.400}',
      500: '{blue.500}',
      600: '{blue.600}',
      700: '{blue.700}',
      800: '{blue.800}',
      900: '{blue.900}',
      950: '{blue.950}'
    },
    colorScheme: {
      light: {
        primary: {
          color: '{blue.900}',
          contrastColor: '#ffffff',
          hoverColor: '{blue.800}',
          activeColor: '{blue.700}'
        },
        highlight: {
          background: '{blue.950}',
          focusBackground: '{blue.700}',
          color: '#ffffff',
          focusColor: '#ffffff'
        }
      }
    }
  }
})

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(PrimeVue, {
        theme: {
          preset: TradingTheme,
          options: {
            prefix: 'p',
            darkModeSelector: 'system',
            cssLayer: false
          }
        }
      })
      .mount(el)
  },
})