<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 bg-gray-100">
    <div class="max-w-md w-full space-y-8">
      <div class="text-center">
        <div class="mx-auto w-16 h-16 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200">
          <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
          </svg>
        </div>
        <h2 class="mt-6 text-3xl font-bold text-gray-900">Buat Akun Baru</h2>
        <p class="mt-2 text-sm text-gray-500">Daftar gratis (Vue)</p>
      </div>

      <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        {{ error }}
      </div>

      <form @submit.prevent="register" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
          <input v-model="form.name" type="text" required
            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all bg-white shadow-sm"
            placeholder="Nama lengkap Anda">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input v-model="form.email" type="email" required
            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all bg-white shadow-sm"
            placeholder="nama@email.com">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input v-model="form.password" type="password" required
            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all bg-white shadow-sm"
            placeholder="Minimal 8 karakter">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
          <input v-model="form.password_confirmation" type="password" required
            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all bg-white shadow-sm"
            placeholder="Ulangi password">
        </div>
        <button type="submit" :disabled="loading"
          class="w-full py-3 px-4 text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 shadow-lg shadow-emerald-200 transition-all active:scale-[0.97] disabled:opacity-50">
          <span v-if="loading">Memproses...</span>
          <span v-else>Daftar Sekarang</span>
        </button>
      </form>

      <p class="text-center text-sm text-gray-500">
        Sudah punya akun?
        <router-link to="/vue/login" class="font-medium text-emerald-600 hover:text-emerald-500">Masuk di sini</router-link>
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
const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });

async function register() {
  loading.value = true;
  error.value = '';
  try {
    const res = await axios.post('/vue/register', form);
    window.__user = res.data.user;
    router.push({ name: 'chat' });
  } catch (e) {
    error.value = e.response?.data?.message || Object.values(e.response?.data?.errors || {}).flat()[0] || 'Registrasi gagal.';
  } finally {
    loading.value = false;
  }
}
</script>
