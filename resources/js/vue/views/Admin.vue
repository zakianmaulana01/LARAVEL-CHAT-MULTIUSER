<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pengguna dan pantau aktivitas</p>
          </div>
          <router-link to="/vue/chat" class="inline-flex items-center px-4 py-2 bg-emerald-500 text-white text-sm font-medium rounded-xl hover:bg-emerald-600 transition-colors shadow-sm active:scale-[0.97]">
            Kembali ke Chat
          </router-link>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div v-for="(stat, idx) in statCards" :key="idx"
          class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500">{{ stat.label }}</p>
              <p class="text-3xl font-bold text-gray-900 mt-1">{{ stat.value }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="stat.bgClass">
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="flex space-x-1 bg-gray-100 rounded-xl p-1 mb-6 max-w-xs">
        <button @click="activeTab = 'users'" :class="activeTab === 'users' ? 'bg-white shadow-sm' : ''"
          class="flex-1 py-2 px-4 text-sm font-medium rounded-lg transition-all">Pengguna</button>
        <button @click="activeTab = 'messages'" :class="activeTab === 'messages' ? 'bg-white shadow-sm' : ''"
          class="flex-1 py-2 px-4 text-sm font-medium rounded-lg transition-all">Pesan</button>
      </div>

      <!-- Users Table -->
      <div v-if="activeTab === 'users'" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Pengguna</th>
              <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
              <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="u in users" :key="u.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold">
                    {{ u.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ u.name }}</p>
                    <p class="text-xs text-gray-500">{{ u.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium" :class="u.role === 'superadmin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'">
                  {{ u.role }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium" :class="u.is_banned ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'">
                  {{ u.is_banned ? 'Diblokir' : 'Aktif' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div v-if="u.role !== 'superadmin'" class="flex items-center justify-end space-x-2">
                  <button @click="banUser(u)" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors active:scale-[0.97]"
                    :class="u.is_banned ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100'">
                    {{ u.is_banned ? 'Aktifkan' : 'Blokir' }}
                  </button>
                  <button @click="deleteUser(u)" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors active:scale-[0.97]">
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Messages -->
      <div v-if="activeTab === 'messages'" class="space-y-3">
        <div v-for="msg in adminMessages" :key="msg.id"
          class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
          <div class="flex items-start justify-between">
            <div class="flex items-start space-x-3 flex-1">
              <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ msg.sender?.name?.charAt(0)?.toUpperCase() || 'U' }}
              </div>
              <div>
                <div class="flex items-center space-x-2">
                  <span class="text-sm font-semibold text-gray-900">{{ msg.sender?.name || 'User' }}</span>
                  <span class="text-xs text-gray-400">{{ formatDate(msg.created_at) }}</span>
                </div>
                <p class="text-sm text-gray-700 mt-1" :class="{ 'line-through opacity-50': msg.deleted_by_superadmin }">{{ msg.body }}</p>
              </div>
            </div>
            <button v-if="!msg.deleted_by_superadmin" @click="deleteMessage(msg)"
              class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors active:scale-[0.97]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const stats = ref({});
const users = ref([]);
const adminMessages = ref([]);
const activeTab = ref('users');

const statCards = computed(() => [
  { label: 'Total Pengguna', value: stats.value.total_users || 0, bgClass: 'bg-blue-50', iconClass: 'text-blue-500' },
  { label: 'Pesan Hari Ini', value: stats.value.messages_today || 0, bgClass: 'bg-emerald-50', iconClass: 'text-emerald-500' },
  { label: 'Chat Aktif', value: stats.value.active_conversations || 0, bgClass: 'bg-purple-50', iconClass: 'text-purple-500' },
  { label: 'User Diblokir', value: stats.value.banned_users || 0, bgClass: 'bg-red-50', iconClass: 'text-red-500' },
]);

onMounted(async () => {
  const [dashRes, usersRes, msgsRes] = await Promise.all([
    axios.get('/vue/admin/dashboard'),
    axios.get('/vue/admin/users'),
    axios.get('/vue/admin/messages'),
  ]);
  stats.value = dashRes.data.stats;
  users.value = usersRes.data.users.data || [];
  adminMessages.value = msgsRes.data.messages.data || [];
});

async function banUser(u) {
  await axios.post(`/vue/admin/users/${u.id}/ban`);
  u.is_banned = !u.is_banned;
}

async function deleteUser(u) {
  if (!confirm(`Yakin hapus ${u.name}?`)) return;
  await axios.delete(`/vue/admin/users/${u.id}`);
  users.value = users.value.filter(x => x.id !== u.id);
}

async function deleteMessage(msg) {
  if (!confirm('Yakin hapus pesan ini?')) return;
  await axios.delete(`/vue/admin/messages/${msg.id}`);
  msg.deleted_by_superadmin = true;
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
</script>
