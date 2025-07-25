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
    // Trading-specific colors
    success: {
      50: '{green.50}',
      100: '{green.100}',
      200: '{green.200}',
      300: '{green.300}',
      400: '{green.400}',
      500: '{green.500}',
      600: '{green.600}',
      700: '{green.700}',
      800: '{green.800}',
      900: '{green.900}',
      950: '{green.950}'
    },
    warn: {
      50: '{yellow.50}',
      100: '{yellow.100}',
      200: '{yellow.200}',
      300: '{yellow.300}',
      400: '{yellow.400}',
      500: '{yellow.500}',
      600: '{yellow.600}',
      700: '{yellow.700}',
      800: '{yellow.800}',
      900: '{yellow.900}',
      950: '{yellow.950}'
    },
    danger: {
      50: '{red.50}',
      100: '{red.100}',
      200: '{red.200}',
      300: '{red.300}',
      400: '{red.400}',
      500: '{red.500}',
      600: '{red.600}',
      700: '{red.700}',
      800: '{red.800}',
      900: '{red.900}',
      950: '{red.950}'
    },
    info: {
      50: '{sky.50}',
      100: '{sky.100}',
      200: '{sky.200}',
      300: '{sky.300}',
      400: '{sky.400}',
      500: '{sky.500}',
      600: '{sky.600}',
      700: '{sky.700}',
      800: '{sky.800}',
      900: '{sky.900}',
      950: '{sky.950}'
    },
    colorScheme: {
      light: {
        primary: {
          color: '{blue.900}',
          contrastColor: '#ffffff',
          hoverColor: '{blue.800}',
          activeColor: '{blue.700}'
        },
        // Success states (profit/wins)
        success: {
          color: '{green.600}',
          contrastColor: '#ffffff',
          hoverColor: '{green.700}',
          activeColor: '{green.800}'
        },
        // Warning states (neutral/breakeven)
        warn: {
          color: '{yellow.500}',
          contrastColor: '{yellow.950}',
          hoverColor: '{yellow.600}',
          activeColor: '{yellow.700}'
        },
        // Danger states (loss/risk)
        danger: {
          color: '{red.600}',
          contrastColor: '#ffffff',
          hoverColor: '{red.700}',
          activeColor: '{red.800}'
        },
        // Info states (general information)
        info: {
          color: '{sky.600}',
          contrastColor: '#ffffff',
          hoverColor: '{sky.700}',
          activeColor: '{sky.800}'
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