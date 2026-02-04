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
        primary: {
          color: '{blue.400}',
          contrastColor: '{gray.900}',
          hoverColor: '{blue.300}',
          activeColor: '{blue.200}'
        },
        highlight: {
          background: 'rgba(96, 165, 250, 0.16)',
          focusBackground: 'rgba(96, 165, 250, 0.24)',
          color: 'rgba(255,255,255,0.87)',
          focusColor: 'rgba(255,255,255,0.87)'
        },
        surface: {
          0: '{gray.900}', // Cards/Containers
          50: '{gray.950}', // Body
          100: '{gray.800}',
          200: '{gray.700}',
          300: '{gray.600}',
          400: '{gray.500}',
          500: '{gray.400}',
          600: '{gray.300}',
          700: '{gray.200}',
          800: '{gray.100}',
          900: '{gray.50}',
          950: '#ffffff'
        },
        text: {
          color: '{gray.100}',
          hoverColor: '#ffffff',
          mutedColor: '{gray.400}',
          highlightColor: '{blue.400}'
        },
        content: {
          background: '{gray.900}',
          hoverBackground: '{gray.800}',
          borderColor: '{gray.700}',
          color: '{gray.100}',
          hoverColor: '#ffffff'
        },
        border: {
          color: '{gray.700}',
          hoverColor: '{gray.600}'
        },
        overlay: {
          select: {
            background: '{gray.900}',
            borderColor: '{gray.700}',
            color: '{gray.100}'
          }
        }
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
  },
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
    },
    inputtext: {
      colorScheme: {
        light: {
          root: {
            background: '{surface.0}',
            disabledBackground: '{surface.100}',
            filledBackground: '{surface.50}',
            borderColor: '{border.color}',
            color: '{text.color}',
            disabledColor: '{text.mutedColor}',
            placeholderColor: '{text.mutedColor}'
          }
        },
        dark: {
          root: {
            background: '{surface.100}',
            disabledBackground: '{surface.200}',
            filledBackground: '{surface.100}',
            borderColor: '{border.color}',
            hoverBorderColor: '{border.hoverColor}',
            focusBorderColor: '{primary.color}',
            invalidBorderColor: '{red.400}',
            color: '{text.color}',
            disabledColor: '{text.mutedColor}',
            placeholderColor: '{text.mutedColor}',
            invalidPlaceholderColor: '{red.400}',
            shadow: '0 0 #0000',
            paddingX: '0.75rem',
            paddingY: '0.5rem',
            borderRadius: '{border.radius.md}',
            focusRing: {
              width: '1px',
              style: 'solid',
              color: '{primary.color}',
              offset: '0',
              shadow: 'none'
            },
            transitionDuration: '{transition.duration}'
          }
        }
      }
    },
    textarea: {
      colorScheme: {
        light: {
          root: {
            background: '{surface.0}',
            disabledBackground: '{surface.100}',
            filledBackground: '{surface.50}',
            borderColor: '{border.color}',
            color: '{text.color}',
            disabledColor: '{text.mutedColor}',
            placeholderColor: '{text.mutedColor}'
          }
        },
        dark: {
          root: {
            background: '{surface.100}',
            disabledBackground: '{surface.200}',
            filledBackground: '{surface.100}',
            borderColor: '{border.color}',
            color: '{text.color}',
            disabledColor: '{text.mutedColor}',
            placeholderColor: '{text.mutedColor}'
          }
        }
      }
    },
    select: {
      colorScheme: {
        light: {
          root: {
            background: '{surface.0}',
            disabledBackground: '{surface.100}',
            filledBackground: '{surface.50}',
            borderColor: '{border.color}',
            color: '{text.color}',
            disabledColor: '{text.mutedColor}',
            placeholderColor: '{text.mutedColor}'
          },
          overlay: {
            background: '{surface.0}',
            borderColor: '{border.color}',
            color: '{text.color}'
          },
          option: {
            focusBackground: '{surface.100}',
            selectedBackground: '{primary.50}',
            selectedFocusBackground: '{primary.100}',
            color: '{text.color}',
            focusColor: '{text.hoverColor}',
            selectedColor: '{primary.700}',
            selectedFocusColor: '{primary.700}'
          }
        },
        dark: {
          root: {
            background: '{surface.100}',
            disabledBackground: '{surface.200}',
            filledBackground: '{surface.100}',
            borderColor: '{border.color}',
            color: '{text.color}',
            disabledColor: '{text.mutedColor}',
            placeholderColor: '{text.mutedColor}'
          },
          overlay: {
            background: '{surface.0}',
            borderColor: '{border.color}',
            color: '{text.color}'
          },
          option: {
            focusBackground: '{surface.100}',
            selectedBackground: 'rgba(96, 165, 250, 0.16)',
            selectedFocusBackground: 'rgba(96, 165, 250, 0.24)',
            color: '{text.color}',
            focusColor: '{text.hoverColor}',
            selectedColor: '{primary.400}',
            selectedFocusColor: '{primary.300}'
          }
        }
      }
    },
    drawer: {
      colorScheme: {
        light: {
          root: {
            background: '{surface.0}',
            borderColor: '{content.borderColor}',
            color: '{text.color}'
          }
        },
        dark: {
          root: {
            background: '{surface.0}',
            borderColor: '{content.borderColor}',
            color: '{text.color}'
          }
        }
      }
    },
    checkbox: {
      colorScheme: {
        light: {
          root: {
            background: '{surface.0}',
            borderColor: '{surface.300}',
            hoverBorderColor: '{primary.color}',
            checkedBackground: '{primary.color}',
            checkedBorderColor: '{primary.color}'
          }
        },
        dark: {
          root: {
            background: '{surface.100}',
            borderColor: '{surface.400}',
            hoverBorderColor: '{primary.color}',
            checkedBackground: '{primary.color}',
            checkedBorderColor: '{primary.color}'
          }
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
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(PrimeVue, {
        theme: {
          preset: TradingTheme,
          options: {
            prefix: 'p',
            darkModeSelector: '.app-dark',
            cssLayer: false
          }
        }
      })
      .use(ConfirmationService)
      .use(ToastService)
      .use(ZiggyVue)

    // Global error handler for production
    app.config.errorHandler = (err, instance, info) => {
      console.error('Global error:', err)
      console.error('Component:', instance)
      console.error('Error info:', info)

      // Send error to toast service if available
      if (app.config.globalProperties.$toast) {
        app.config.globalProperties.$toast.add({
          severity: 'error',
          summary: 'Application Error',
          detail: err.message || 'An unexpected error occurred',
          life: 5000
        })
      }

      // In production, you could send to error tracking service
      // e.g., Sentry.captureException(err)
    }

    // Global warning handler (optional - for development)
    app.config.warnHandler = (msg, instance, trace) => {
      console.warn('Vue warning:', msg)
      console.warn('Trace:', trace)
    }

    app.mount(el)
  },
})