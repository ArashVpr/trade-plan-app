<script setup>
import { computed } from 'vue';

const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: (value) => ['bot', 'user'].includes(value),
  },
  content: {
    type: String,
    required: true,
  },
  timestamp: {
    type: Date,
    default: () => new Date(),
  },
});

const formattedTime = computed(() => {
  return props.timestamp.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
  });
});

const isBot = computed(() => props.type === 'bot');

// Simple markdown rendering for bold text and newlines
const formattedContent = computed(() => {
  return props.content
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Bold
    .replace(/\n/g, '<br>'); // Line breaks
});
</script>

<template>
  <div class="mb-4">
    <!-- Bot message -->
    <div v-if="isBot" class="flex items-start gap-3 animate-in fade-in slide-in-from-bottom-2 duration-300">
      <div class="shrink-0">
        <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-700">
          <i class="pi pi-sparkles text-white text-sm"></i>
        </div>
      </div>
      <div class="flex-1">
        <div
          class="px-4 py-3 rounded-2xl rounded-tl-sm max-w-[85%] bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700 shadow-md sm:max-w-[90%]">
          <div v-html="formattedContent" class="text-sm leading-relaxed [&_strong]:font-semibold"></div>
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 px-1">{{ formattedTime }}</div>
      </div>
    </div>

    <!-- User message -->
    <div v-else class="flex items-start gap-3 justify-end animate-in fade-in slide-in-from-bottom-2 duration-300">
      <div class="flex-1 flex flex-col items-end">
        <div
          class="px-4 py-3 rounded-2xl rounded-tr-sm max-w-[85%] bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 text-white shadow-md sm:max-w-[90%]">
          <div class="text-sm leading-relaxed">{{ content }}</div>
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 px-1">{{ formattedTime }}</div>
      </div>
    </div>
  </div>
</template>
