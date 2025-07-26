<template>
    <Button :label="label" :icon="icon" :severity="severity" :size="size" :outlined="outlined" :text="text"
        :raised="raised" :rounded="rounded" :disabled="disabled" :loading="loading" :class="buttonClass"
        @click="handleClick" v-tooltip="tooltip">
        <template v-if="$slots.default" #default>
            <slot />
        </template>
    </Button>
</template>

<script setup>
const props = defineProps({
    label: String,
    icon: String,
    severity: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'help', 'contrast'].includes(value)
    },
    size: {
        type: String,
        default: 'normal',
        validator: (value) => ['small', 'normal', 'large'].includes(value)
    },
    outlined: {
        type: Boolean,
        default: false
    },
    text: {
        type: Boolean,
        default: false
    },
    raised: {
        type: Boolean,
        default: false
    },
    rounded: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    tooltip: String,
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'action', 'navigation', 'icon-only'].includes(value)
    }
})

const emit = defineEmits(['click'])

const buttonClass = computed(() => {
    const classes = []

    if (props.variant === 'action') {
        classes.push('px-6 py-3 font-semibold')
    } else if (props.variant === 'navigation') {
        classes.push('px-4 py-2')
    } else if (props.variant === 'icon-only') {
        classes.push('p-2')
    }

    return classes.join(' ')
})

const handleClick = (event) => {
    if (!props.disabled && !props.loading) {
        emit('click', event)
    }
}
</script>
