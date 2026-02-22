<template>
  <UHeader toggle-side="left"
    :ui="{
        button: {
          base: 'lg:flex'
        }
      }"
    >
    <template #title>
      <h1>Sistema de Tickets</h1>
    </template>

    <UNavigationMenu :items="items" class="gap-10"/>

    <template #body>
      <UNavigationMenu orientation="vertical" :items="items" />
    </template>

    <template #right>
      <UButton class="cursor-pointer" :label="useAuth.user !== null ? 'Sair': 'Login'" @click="authOrLogout" :color="useAuth.user !== null ? 'error' : 'primary'" variant="subtle" :icon="useAuth.user !== null ? 'i-mdi-logout' : 'i-mdi-login'"/>
    </template>
  </UHeader>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { NavigationMenuItem } from "@nuxt/ui"
import { useAuthStore } from '@/stores/useAuth.ts'

defineOptions({
  name: 'Header'
});

const useAuth = useAuthStore()
const route = useRoute()
const router = useRouter()

const items = computed<NavigationMenuItem[]>(() => [
  {
    label: 'Home',
    to: '/',
    icon: 'i-mdi-home',
    active: route.path == "/"
  },
  {
    label: 'Adicionar Ticket',
    to: '/add-ticket',
    icon: 'i-mdi-add',
    active: route.path == '/add-ticket'
  },
  {
    label: 'Quadro de Tickets',
    to: '/view',
    icon: 'i-lucide-list',
    active: route.path == '/view'
  },

])

const authOrLogout = async () => {
  try {
    if (useAuth.user !== null) {
      useAuth.logout()
      router.replace('/auth')
      return
    }
    
    router.replace('/auth')
  } catch (e) {
    console.log("erro ao sair: " + e)
  }
}
</script>
