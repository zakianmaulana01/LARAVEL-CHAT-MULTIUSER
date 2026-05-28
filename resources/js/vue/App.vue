<template>
  <router-view v-slot="{ Component }">
    <transition name="page" mode="out-in">
      <component :is="Component" />
    </transition>
  </router-view>
</template>

<script setup>
</script>

<style>
.page-enter-active { transition: opacity 250ms cubic-bezier(0.2, 0, 0, 1), transform 250ms cubic-bezier(0.2, 0, 0, 1); }
.page-leave-active { transition: opacity 150ms cubic-bezier(0.2, 0, 0, 1); }
.page-enter-from { opacity: 0; transform: translateY(8px); }
.page-leave-to { opacity: 0; }
</style>
