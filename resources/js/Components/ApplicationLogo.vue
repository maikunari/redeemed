<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

defineProps(['className']);
const page = usePage();
const logo = computed(() => page.props.logo || '');
const siteName = computed(() => page.props.siteName || 'redeem');
</script>

<template>
  <div class="flex items-center gap-2">
    <div v-if="logo" class="h-[40px] w-[40px] rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center p-1">
      <img class="h-full w-full object-contain rounded-full" :src="logo" alt="Site Logo" />
    </div>
    <div class="text-white text-2xl font-black tracking-tight font-['Roboto'] italic drop-shadow-sm">
      {{ siteName }}
    </div>
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@1,900&display=swap');
</style>
