<template>
  <div class="h-screen flex overflow-hidden bg-gray-100">
    <!-- Sidebar -->
    <div class="w-full md:w-96 lg:w-[420px] flex flex-col border-r border-gray-200 bg-white"
         :class="{ 'hidden md:flex': activeConversation }">

      <!-- Header -->
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
            {{ user?.name?.charAt(0).toUpperCase() }}
          </div>
          <div>
            <h1 class="text-lg font-bold text-gray-900">Chat</h1>
            <p class="text-xs text-gray-500">{{ user?.name }}</p>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <router-link v-if="user?.role === 'superadmin'" to="/vue/admin"
            class="p-2 rounded-full hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </router-link>
          <button @click="logout" class="p-2 rounded-full hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Search -->
      <div class="px-4 py-3">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input v-model="searchQuery" @input="searchUsers" type="text"
            placeholder="Cari atau mulai chat baru..."
            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-0 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
        </div>
        <!-- Search results -->
        <div v-if="searchResults.length" class="mt-2 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
          <button v-for="u in searchResults" :key="u.id" @click="startChat(u.id)"
            class="w-full flex items-center px-4 py-3 hover:bg-gray-50 transition-colors active:scale-[0.97]">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">
              {{ u.name.charAt(0).toUpperCase() }}
            </div>
            <div class="ml-3 text-left">
              <p class="text-sm font-medium text-gray-900">{{ u.name }}</p>
              <p class="text-xs text-gray-500">{{ u.email }}</p>
            </div>
          </button>
        </div>
      </div>

      <!-- Conversations -->
      <div class="flex-1 overflow-y-auto">
        <button v-for="conv in conversations" :key="conv.id" @click="openConversation(conv)"
          class="w-full flex items-center px-5 py-3.5 hover:bg-gray-50 transition-all border-b border-gray-50 active:scale-[0.98]"
          :class="{ 'bg-emerald-50 border-l-4 border-l-emerald-500': activeConversation?.id === conv.id }">
          <div class="relative flex-shrink-0">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold shadow-sm">
              {{ getOtherName(conv).charAt(0).toUpperCase() }}
            </div>
          </div>
          <div class="ml-3.5 flex-1 min-w-0 text-left">
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold text-gray-900 truncate">{{ getOtherName(conv) }}</p>
              <span v-if="conv.latest_message" class="text-xs text-gray-400 flex-shrink-0 ml-2">
                {{ formatRelative(conv.latest_message.created_at) }}
              </span>
            </div>
            <div class="flex items-center justify-between mt-0.5">
              <p class="text-sm text-gray-500 truncate">
                {{ conv.latest_message?.body ? conv.latest_message.body.substring(0, 35) : 'Belum ada pesan' }}
              </p>
              <span v-if="conv.unread_count > 0"
                class="ml-2 flex-shrink-0 w-5 h-5 bg-emerald-500 text-white text-xs rounded-full flex items-center justify-center font-bold">
                {{ conv.unread_count > 9 ? '9+' : conv.unread_count }}
              </span>
            </div>
          </div>
        </button>

        <div v-if="!conversations.length" class="flex flex-col items-center justify-center h-64 text-gray-400">
          <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
          <p class="text-sm">Belum ada percakapan</p>
        </div>
      </div>
    </div>

    <!-- Chat Window -->
    <div class="flex-1 flex flex-col bg-gray-50" :class="{ 'hidden md:flex': !activeConversation }">
      <!-- Header -->
      <div v-if="activeConversation" class="flex items-center px-5 py-3.5 bg-white border-b border-gray-100 shadow-sm">
        <button @click="activeConversation = null" class="md:hidden mr-3 p-1 rounded-full hover:bg-gray-100">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-sm">
          {{ activeName.charAt(0).toUpperCase() }}
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-semibold text-gray-900">{{ activeName }}</h3>
          <p class="text-xs" :class="typingUser ? 'text-emerald-500 italic' : 'text-gray-400'">
            {{ typingUser ? typingUser + ' sedang mengetik...' : 'Online' }}
          </p>
        </div>
      </div>

      <!-- Messages -->
      <div ref="messagesContainer" class="flex-1 overflow-y-auto px-4 py-4 space-y-2"
        style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f0f9ff 100%);">
        <div v-if="!activeConversation" class="flex flex-col items-center justify-center h-full text-gray-400">
          <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-12 h-12 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
          </div>
          <p class="text-base font-medium text-gray-500">Pilih percakapan</p>
          <p class="text-sm mt-1">atau cari seseorang untuk mulai chat</p>
        </div>

        <TransitionGroup name="message">
          <div v-for="msg in messages" :key="msg.id"
            :class="msg.sender_id === user?.id || msg.sender?.id === user?.id ? 'flex justify-end' : 'flex justify-start'">
            <div class="max-w-[75%] lg:max-w-[60%]"
              :class="(msg.sender_id === user?.id || msg.sender?.id === user?.id)
                ? 'bg-emerald-500 text-white rounded-2xl rounded-br-md px-4 py-2.5 shadow-sm'
                : 'bg-white text-gray-800 rounded-2xl rounded-bl-md px-4 py-2.5 shadow-sm border border-gray-100'">
              <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ msg.body }}</p>
              <div class="flex items-center justify-end mt-1 space-x-1">
                <span class="text-[10px] opacity-70">{{ formatTime(msg.created_at) }}</span>
              </div>
            </div>
          </div>
        </TransitionGroup>

        <!-- Typing -->
        <div v-if="typingUser" class="flex justify-start">
          <div class="bg-white rounded-2xl rounded-bl-md px-4 py-3 shadow-sm border border-gray-100">
            <div class="flex space-x-1">
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Input -->
      <div v-if="activeConversation" class="px-4 py-3 bg-white border-t border-gray-100">
        <form @submit.prevent="sendMessage" class="flex items-end space-x-3">
          <textarea v-model="newMessage" @keydown.enter.exact.prevent="sendMessage" @input="emitTyping"
            rows="1" placeholder="Ketik pesan..."
            class="flex-1 resize-none border-0 bg-gray-50 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all max-h-32 overflow-y-auto"></textarea>
          <button type="submit" :disabled="!newMessage.trim()"
            class="flex-shrink-0 w-11 h-11 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 transition-all active:scale-[0.97] disabled:opacity-40">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const user = ref(window.__user);
const conversations = ref([]);
const messages = ref([]);
const activeConversation = ref(null);
const activeName = ref('');
const newMessage = ref('');
const searchQuery = ref('');
const searchResults = ref([]);
const typingUser = ref(null);
const messagesContainer = ref(null);
let typingTimeout = null;
let echoChannel = null;

onMounted(async () => {
  await loadConversations();
  if (route.params.id) {
    const conv = conversations.value.find(c => c.id == route.params.id);
    if (conv) openConversation(conv);
  }
  listenNotifications();
});

async function loadConversations() {
  const res = await axios.get('/vue/conversations');
  conversations.value = res.data.conversations;
}

function getOtherName(conv) {
  const other = conv.participants?.find(p => p.id !== user.value?.id);
  return other?.name || 'User';
}

async function openConversation(conv) {
  activeConversation.value = conv;
  activeName.value = getOtherName(conv);
  const res = await axios.get(`/vue/conversations/${conv.id}`);
  messages.value = res.data.messages.data || [];
  await nextTick();
  scrollToBottom();
  subscribeConversation(conv.id);
  axios.post(`/vue/conversations/${conv.id}/read`);
}

function subscribeConversation(id) {
  if (echoChannel) window.Echo.leave(echoChannel);
  echoChannel = `conversation.${id}`;
  window.Echo.join(echoChannel)
    .listen('MessageSent', (e) => {
      messages.value.push(e);
      nextTick(() => scrollToBottom());
      axios.post(`/vue/conversations/${id}/read`);
    })
    .listen('TypingStarted', (e) => {
      typingUser.value = e.user_name;
      clearTimeout(typingTimeout);
      typingTimeout = setTimeout(() => { typingUser.value = null; }, 3000);
    });
}

function listenNotifications() {
  window.Echo.private(`user.${user.value?.id}`)
    .listen('NotificationSent', (e) => {
      const conv = conversations.value.find(c => c.id === e.conversation_id);
      if (conv) conv.unread_count = (conv.unread_count || 0) + 1;
    });
}

async function sendMessage() {
  if (!newMessage.value.trim()) return;
  const body = newMessage.value;
  newMessage.value = '';
  try {
    const res = await axios.post('/vue/messages', {
      conversation_id: activeConversation.value.id,
      body,
    });
    messages.value.push(res.data.message);
    await nextTick();
    scrollToBottom();
  } catch (e) {
    newMessage.value = body;
  }
}

function emitTyping() {
  if (activeConversation.value) {
    axios.post('/vue/typing', { conversation_id: activeConversation.value.id });
  }
}

let searchTimeout = null;
function searchUsers() {
  clearTimeout(searchTimeout);
  if (searchQuery.value.length < 2) { searchResults.value = []; return; }
  searchTimeout = setTimeout(async () => {
    const res = await axios.get(`/vue/users/search?q=${encodeURIComponent(searchQuery.value)}`);
    searchResults.value = res.data.users || [];
  }, 300);
}

async function startChat(userId) {
  const res = await axios.post('/vue/conversations/start', { user_id: userId });
  searchQuery.value = '';
  searchResults.value = [];
  await loadConversations();
  openConversation(res.data.conversation);
}

async function logout() {
  await axios.post('/vue/logout');
  window.__user = null;
  router.push({ name: 'login' });
}

function scrollToBottom() {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
}

function formatTime(dateStr) {
  return new Date(dateStr).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

function formatRelative(dateStr) {
  const diff = Date.now() - new Date(dateStr).getTime();
  const mins = Math.floor(diff / 60000);
  if (mins < 1) return 'baru';
  if (mins < 60) return `${mins}m`;
  const hours = Math.floor(mins / 60);
  if (hours < 24) return `${hours}j`;
  return `${Math.floor(hours / 24)}h`;
}
</script>

<style scoped>
.message-enter-active { transition: all 200ms cubic-bezier(0.2, 0, 0, 1); }
.message-enter-from { opacity: 0; transform: translateY(4px) scale(0.98); }
</style>
