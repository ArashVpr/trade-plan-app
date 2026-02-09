<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import ChatMessage from './ChatMessage.vue';
import ChatOptionButtons from './ChatOptionButtons.vue';
import { chatbotTree } from '@/Data/chatbotTree.js';

const visible = defineModel('visible', { type: Boolean, default: false });

// State
const messages = ref([]);
const currentNodeId = ref('welcome');
const isProcessing = ref(false);
const messagesEndRef = ref(null);

// Computed
const currentNode = computed(() => chatbotTree[currentNodeId.value]);

const hasOptions = computed(() => {
  return currentNode.value?.options && currentNode.value.options.length > 0;
});

// Methods
const initializeChat = () => {
  messages.value = [];
  currentNodeId.value = 'welcome';

  // Add welcome message
  setTimeout(() => {
    addBotMessage(chatbotTree.welcome.message);
  }, 300);
};

const addBotMessage = (content) => {
  messages.value.push({
    type: 'bot',
    content,
    timestamp: new Date(),
  });
  scrollToBottom();
};

const addUserMessage = (content) => {
  messages.value.push({
    type: 'user',
    content,
    timestamp: new Date(),
  });
  scrollToBottom();
};

const handleOptionSelect = async (option) => {
  if (isProcessing.value) return;

  isProcessing.value = true;

  // Add user's choice to chat
  addUserMessage(option.label);

  // Handle navigation links
  if (option.isLink) {
    await nextTick();

    // Small delay for better UX
    setTimeout(() => {
      navigateToRoute(option.nextNodeId);
      isProcessing.value = false;
    }, 500);
    return;
  }

  // Navigate to next node in decision tree
  setTimeout(() => {
    navigateToNode(option.nextNodeId);
    isProcessing.value = false;
  }, 600);
};

const navigateToNode = (nodeId) => {
  if (!chatbotTree[nodeId]) {
    console.error(`Node "${nodeId}" not found in chatbot tree`);
    return;
  }

  currentNodeId.value = nodeId;
  addBotMessage(chatbotTree[nodeId].message);
};

const navigateToRoute = (routeName) => {
  // Close the chatbot drawer
  visible.value = false;

  // Navigate using Inertia router
  try {
    if (routeName === 'home') {
      router.get(route('home'));
    } else if (routeName === 'dashboard') {
      router.get(route('dashboard'));
    } else if (routeName === 'checklist-weights.index') {
      router.get(route('checklist-weights.index'));
    } else {
      console.warn(`Route "${routeName}" not configured`);
    }
  } catch (error) {
    console.error('Navigation error:', error);
  }
};

const resetConversation = () => {
  messages.value = [];
  currentNodeId.value = 'welcome';
  nextTick(() => {
    addBotMessage(chatbotTree.welcome.message);
  });
};

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesEndRef.value) {
      messagesEndRef.value.scrollIntoView({ behavior: 'smooth' });
    }
  });
};

const handleClose = () => {
  visible.value = false;
};

// Watch for drawer open/close
watch(visible, (newVal) => {
  if (newVal && messages.value.length === 0) {
    initializeChat();
  }
});
</script>

<template>
  <Drawer v-model:visible="visible" position="right" class="chatbot-drawer" :style="{ width: '450px' }">
    <template #header>
      <div class="flex items-center gap-3">
        <div
          class="w-10 h-10 rounded-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-700">
          <i class="pi pi-sparkles text-white"></i>
        </div>
        <div>
          <h2 class="text-lg font-bold text-primary-900 dark:text-primary-100">
            Strategy Guide
          </h2>
          <p class="text-xs text-surface-600 dark:text-surface-400">
            Learn about the app
          </p>
        </div>
      </div>
    </template>

    <!-- Messages Area -->
    <div class="flex flex-col h-full">
      <div class="flex-1 overflow-y-auto px-4 py-4 bg-gray-100 dark:bg-gray-950">
        <ChatMessage v-for="(message, index) in messages" :key="index" :type="message.type" :content="message.content"
          :timestamp="message.timestamp" />

        <!-- Scroll anchor -->
        <div ref="messagesEndRef"></div>
      </div>

      <!-- Options Area -->
      <div v-if="hasOptions" class="border-t border-gray-200 dark:border-gray-700 px-4 py-4 bg-white dark:bg-gray-900">
        <ChatOptionButtons :options="currentNode.options" :disabled="isProcessing"
          @select-option="handleOptionSelect" />
      </div>

      <!-- Action Buttons -->
      <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 flex gap-2 bg-white dark:bg-gray-900">
        <Button label="Start Over" icon="pi pi-refresh" @click="resetConversation" outlined size="small"
          class="flex-1" />
        <Button label="Close" icon="pi pi-times" @click="handleClose" outlined severity="secondary" size="small"
          class="flex-1" />
      </div>
    </div>
  </Drawer>
</template>

<style scoped>
/* Mobile responsive */
@media (max-width: 640px) {
  .chatbot-drawer {
    width: 100vw !important;
  }
}
</style>
