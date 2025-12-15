<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import AuthWarningModal from './components/modals/AuthWarningModal.vue'

const router = useRouter()
const authWarningRef = ref(null)

onMounted(() => {
  fetch('/api/v1/health')
    .then(r => r.json())
    .then(data => {
      console.log('API Status:', data)
    })
    .catch(error => {
      console.error('API Error:', error)
    })
})

// Provide method to trigger warning from router guard
window.showAuthWarning = () => {
  if (authWarningRef.value) {
    authWarningRef.value.showWarning()
  }
}
</script>

<template>
  <AuthWarningModal ref="authWarningRef" />
  <router-view />
</template>

<style>
/* Global styles are in style.css */
</style>
