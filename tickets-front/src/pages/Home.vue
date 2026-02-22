<template>
  <UContainer class="min-h-screen bg-red-500 p-6">
    <h1 class="text-3xl">Bernardo</h1>
  </UContainer>
</template>

<script setup lang='ts'>
  import { useAuthStore } from '@/stores/useAuth.ts'
  import { ref, onMounted } from 'vue'
  import { http } from '../api/api.ts'
  import { useRouter } from 'vue-router'
  const message = ref<string>('')
  
  const store = useAuthStore()
  const router = useRouter()

  onMounted(async () => {
  try {
      if (store.user === null) {
        router.replace('/auth')
        return
      }
      const response = await http.get('/api/home') 

      if (response.data) {
        message.value = response.data.message
      }
  } catch (error) {
      console.error("Erro na requisição:", error)
      message.value = "Erro ao carregar"
  }  })
</script>
