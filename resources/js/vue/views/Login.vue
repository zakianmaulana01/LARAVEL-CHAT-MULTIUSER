<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 bg-gray-100">
    <div class="max-w-md w-full space-y-8">
      <div class="text-center">
        <div class="mx-auto w-16 h-16 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200">
          <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
        </div>
        <h2 class="mt-6 text-3xl font-bold text-gray-900">Selamat Datang</h2>
        <p class="mt-2 text-sm text-gray-500">Masuk ke akun Anda (Vue)</p>
      </div>

      <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        {{ error }}
      </div>

      <form @submit.prevent="login" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input v-model="form.email" type="email" required
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all bg-white shadow-sm"
            placeholder="nama@email.com">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input v-model="form.password" type="password" required
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all bg-white shadow-sm"
            placeholder="Masukkan password">
        </div>
        <button type="submit" :disabled="loading"
          class="w-full py-3 px-4 text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 shadow-lg shadow-emerald-200 transition-all active:scale-[0.97] disabled:opacity-50">
          <span v-if="loading">Memproses...</span>
          <span v-else>Masuk</span>
        </button>
      </form>

      <p class="text-center text-sm text-gray-500">
        Belum punya akun?
        <router-link to="/vue/register" class="font-medium text-emerald-600 hover:text-emerald-500">Daftar sekarang</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const loading = ref(false);
const error = ref('');
const form = reactive({ email: '', password: '' });

async function login() {
  loading.value = true;
  error.value = '';
  try {
    const res = await axios.post('/vue/login', form);
    window.__user = res.data.user;
    router.push({ name: 'chat' });
  } catch (e) {
    error.value = e.response?.data?.message || 'Login gagal.';
  } finally {
    loading.value = false;
  }
}
</script>
