<script setup>
const props = defineProps({
  options: {
    type: Array,
    required: true,
    default: () => [],
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['select-option']);

const handleSelect = (option) => {
  if (!props.disabled) {
    emit('select-option', option);
  }
};
</script>

<template>
  <div class="mt-3 mb-2 animate-in fade-in slide-in-from-bottom-2 duration-400">
    <div class="flex flex-col gap-2">
      <Button v-for="(option, index) in options" :key="index" :label="option.label" @click="handleSelect(option)"
        :disabled="disabled" outlined
        class="w-full !justify-start !text-left !border-2 !border-primary-300 dark:!border-primary-600 !text-primary-700 dark:!text-primary-300 hover:!bg-primary-50 dark:hover:!bg-primary-900/30 hover:!border-primary-500 dark:hover:!border-primary-400 transition-all duration-200 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed !min-h-[44px] hover:scale-[1.02]"
        size="small" />
    </div>
  </div>
</template>
