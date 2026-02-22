<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <UContainer class="min-h-[calc(100vh-12rem)] flex items-center justify-center p-6">
  <UCard class="max-w-xl w-full bg-green-100 shadow-xl">
  <template #header>
    <h1 class="text-3xl font-bold text-center">Bem-vindo!</h1>
  </template>

  <UTabs :items="items" size="xl">
  <template #login class="space-y-4">
    <UFormField label="E-Mail" class="text-lg mt-4">
    <UInput v-model="email" type="email" placeholder="Seu e-mail" size="xl" class="w-full" />
    </UFormField>
    <UFormField label="Senha" class="text-lg mt-4">
    <UInput v-model="password" type="password" placeholder="Sua senha" size="xl" class="w-full" />
    </UFormField>

    <UButton class="w-full cursor-pointer flex items-center justify-center mt-10 text-lg font-bold" :loading="loading"
             @click="handleEmailLogin" size="xl">Entrar
    </UButton>

    <p v-if="errorMsg" class="text-red-500 text-sm">{{ errorMsg }}</p>
  </template>

  <template #register class="space-y-4">
    <UFormField label="Nome" class="text-lg mt-4">
    <UInput v-model="name" type="text" placeholder="Seu nome" size="xl" class="w-full" />
    </UFormField>

    <UFormField label="E-Mail" class="text-lg mt-4">
    <UInput v-model="email" type="email" placeholder="Seu e-mail" size="xl" class="w-full" />
    </UFormField>
    <UFormField label="Senha" class="text-lg mt-4">
    <UInput v-model="password" type="password" placeholder="Sua senha" size="xl" class="w-full" />
    </UFormField>
    <UFormField label='Posição' class="text-lg mt-4">
    <USelect v-model="role" :items="selectItems" size="xl" class="w-full" placeholder="Escolha sua posição"/>
    </UFormField>

    <UButton class="w-full cursor-pointer mt-10 flex items-center justify-center text-lg font-bold"
             color="neutral" :loading="loading" size="xl" @click="handleRegister">
    Registrar
    </UButton>

    <p v-if="errorMsg" class="text-red-500 text-sm">{{ errorMsg }}</p>
  </template>
  </UTabs>
  </UCard>
  </UContainer>
</template>

<script setup lang="ts">
  import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import type { TabsItem, SelectItem } from "@nuxt/ui";
import { useAuthStore } from '@/stores/useAuth'

const authStore = useAuthStore()
const router = useRouter();
const route = useRoute();

const name = ref("");
const email = ref("");
const password = ref("");
const role = ref('')
const selectItems = ref<SelectItem[]>([
  {
  label: 'Desenvolvedor',
  value: 'DESENVOLVEDOR'
  },
  {
  label: 'Gerente',
  value: 'GERENTE'
  },
  {
  label: 'Chefe',
  value: 'CHEFE'
  },
  {
  label: 'Analista',
  value: 'ANALISTA'
  },
  {
  label: 'Redator',
  value: 'REDATOR'
  },
  {
  label: 'Colunista',
  value: 'COLUNISTA'
  }
])
const loading = ref(false);
const errorMsg = ref("");

const items = ref<TabsItem[]>([
  { label: "Entrar", value: "login", slot: "login" },
  { label: "Registrar", value: "register", slot: "register" },
])

const redirectToHome = () => {
  router.push('/')
}

const handleEmailLogin = async () => {
try {
    loading.value = true
    await authStore.login(email.value, password.value)
    redirectToHome()
  } catch (error: any) {
    const message = error?.response?.data?.message || 'Erro ao realizar login'
  } finally {
    loading.value = false
  }
}

const handleRegister = async () => {
  try {
    loading.value = true
    await authStore.register(
      name.value,
      email.value,
      password.value,
      role.value
    )
    redirectToHome()
  } catch (error: any) {
    const message = error?.response?.data?.message || 'Erro ao cadastrar'
  } finally {
    loading.value = false
  }
}
</script>
