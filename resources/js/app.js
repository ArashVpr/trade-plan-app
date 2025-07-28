import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import PrimeVue from 'primevue/config'
import ConfirmationService from 'primevue/confirmationservice'
import ToastService from 'primevue/toastservice'
import { ZiggyVue } from 'ziggy-js'
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
        },
        // Surface colors for cards and panels
        surface: {
          0: '#ffffff',
          50: '{gray.50}',
          100: '{gray.100}',
          200: '{gray.200}',
          300: '{gray.300}',
          400: '{gray.400}',
          500: '{gray.500}',
          600: '{gray.600}',
          700: '{gray.700}',
          800: '{gray.800}',
          900: '{gray.900}',
          950: '{gray.950}'
        },
        // Text colors
        text: {
          color: '{gray.800}',
          hoverColor: '{gray.900}',
          mutedColor: '{gray.600}',
          highlightColor: '{blue.900}'
        },
        // Content colors
        content: {
          background: '#ffffff',
          hoverBackground: '{gray.50}',
          borderColor: '{gray.200}',
          color: '{gray.700}',
          hoverColor: '{gray.800}'
        },
        // Border and overlay
        border: {
          color: '{gray.200}',
          hoverColor: '{gray.300}'
        },
        overlay: {
          select: {
            background: 'rgba(255,255,255,0.8)',
            borderColor: '{gray.200}',
            color: '{gray.800}'
          }
        }
      },
      dark: {
        // Dark theme colors for your trading app
        primary: { color: '{blue.400}' },
        surface: { 0: '{gray.950}', 50: '{gray.900}' },
        text: { color: '{gray.200}' }
      }
    },
    // Add this to your theme for specific component styling
    components: {
      button: {
        borderRadius: '6px',
        paddingX: '1rem',
        paddingY: '0.5rem'
      },
      card: {
        shadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
        borderRadius: '8px'
      },
      datatable: {
        headerBackground: '{surface.100}',
        headerBorderColor: '{border.color}'
      }
    },
    typography: {
      fontFamily: '"Inter", "Segoe UI", sans-serif',
      fontSize: {
        xs: '0.75rem',
        sm: '0.875rem', 
        base: '1rem',
        lg: '1.125rem',
        xl: '1.25rem'
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
      .use(ConfirmationService)
      .use(ToastService)
      .use(ZiggyVue)
      .mount(el)
  },
})