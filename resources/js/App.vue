<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  fetch('/api/ping')
    .then(r => r.json())
    .then(console.log)
})
</script>

<template>
  <router-view />
</template>

<style>
/* Global styles are in style.css */
</style>
