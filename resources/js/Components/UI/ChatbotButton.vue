<script setup>
import { computed } from 'vue';

const props = defineProps({
  visible: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['toggle']);

const buttonLabel = computed(() => {
  return props.visible ? 'Close Guide' : 'Strategy Guide';
});

const handleClick = () => {
  emit('toggle');
};
</script>

<template>
  <div class="fixed bottom-6 right-6 z-50">
    <Button :label="buttonLabel" icon="pi pi-question-circle" @click="handleClick"
      class="transition-all duration-300 border-0 !text-white font-semibold min-w-[60px] min-h-[60px] shadow-lg hover:scale-105 active:scale-95"
      :class="visible ? 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800' : 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800'"
      rounded size="large" aria-label="Toggle Strategy Guide chatbot">
      <template #icon>
        <i :class="visible ? 'pi pi-times' : 'pi pi-question-circle'" class="text-xl"></i>
      </template>
    </Button>
  </div>
</template>
