<template>
  <UContainer class="min-h-screen w-full flex items-start gap-2 py-10">
    <UCard class='w-2/3 shadow-xl'>
      <template #header>
        <span class="text-secondary font-semibold">Título</span>
        <h1 class="text-xl">{{ticket.title}}</h1>
        <div class="flex wrap items-center gap-4 mt-2">
          <UBadge v-for="tag in badges" :key="tag" variant="subtle" :label="tag" class="rounded-full"/>
        </div>
        <div class="grid grid-cols-4 gap-5 mt-10">
          <div class="flex flex-col">
            <span class="font-semibold text-secondary text-sm">Prioridade</span>
            <label>{{ticket.priority}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary text-sm">Aberto em</span>
            <label>{{formattedDate(ticket.created_at)}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary text-sm">Deadline</span>
            <label>{{formattedDate(ticket.due_date)}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary text-sm">Status</span>
            <label>{{ticket.status}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary text-sm">Aberto por</span>
            <label>{{ticket.owner}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary text-sm">Grupo responsável</span>
            <label>{{ticket.team}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary text-sm">Usuário responsável</span>
            <label>{{ticket.responsable}}</label>
          </div>
        </div>
      </template>
      <span class="text-secondary font-semibold text-sm">Descrição</span>
      <div v-html="ticket.description" class="conteudo-editor"></div>
    </UCard>
    
    <UCard class="w-1/3 shadow-xl">
      <div class="flex flex-col gap-4">
        <UButton size="xl" class="flex items-center justify-center cursor-pointer" color="primary"><UIcon name="i-mdi-send" class="size-6"/>Comentar</UButton>
        <UButton size="xl" class="flex items-center justify-center cursor-pointer" color="secondary"><UIcon name="i-mdi-undo" class="size-6"/>Devolver</UButton>
        <UButton size="xl" class="flex items-center justify-center cursor-pointer" color="warning"><UIcon name="i-mdi-sync" class="size-6"/>Atualizar</UButton>
        <UButton size="xl" class="flex items-center justify-center cursor-pointer" color="error"><UIcon name="i-mdi-check" class="size-6"/>Finalizar</UButton>
        <UButton size="xl" class="flex items-center justify-center cursor-pointer" color="info"><UIcon name="i-mdi-format-list-bulleted" class="size-6"/>Lista</UButton>
      </div>
    </UCard>
  </UContainer>
</template>

<script setup lang='ts'>
  import { ref, onMounted, computed } from 'vue'
  import { useRoute } from 'vue-router'
  import useTickets from '@/composables/useTickets.ts'
  import type { Tickets } from '@/types/types.ts'

  const route = useRoute()
  const { getTicket } = useTickets()

  const ticket = ref<Tickets>({})

  onMounted(async () => {
    const currentTicket = await getTicket(route.params.id)
    ticket.value = currentTicket
  })

  const badges = computed(() => {
   if (!ticket.value?.tags) return []

   const tags = ticket.value.tags as string
   return tags.split(", ")
  })
 
  const formattedDate = (date: Date | string) => {
    return new Date(date).toLocaleString('pt-BR',{
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  }
</script>

<style scoped>
@reference "../style.css";
.conteudo-editor :deep(h1) {
  @apply text-3xl font-bold mb-6 mt-8 text-gray-900 dark:text-white border-b pb-2;
}

.conteudo-editor :deep(h2) {
  @apply text-2xl font-semibold mb-4 mt-6 text-gray-800 dark:text-gray-100;
}

.conteudo-editor :deep(strong), 
.conteudo-editor :deep(b) {
  @apply font-bold text-primary-500; 
}

.conteudo-editor :deep(em), 
.conteudo-editor :deep(i) {
  @apply italic text-gray-700 dark:text-gray-300;
}

.conteudo-editor :deep(ul) {
  @apply list-disc ml-6 mb-4 space-y-2;
}

.conteudo-editor :deep(ol) {
  @apply list-decimal ml-6 mb-4 space-y-2;
}

.conteudo-editor :deep(li) {
  @apply pl-1;
}

.conteudo-editor :deep(blockquote) {
  @apply border-l-4 border-primary-500 pl-4 py-2 my-4 italic bg-gray-50 dark:bg-gray-800 rounded-r;
}

.conteudo-editor :deep(p) {
  @apply mb-4 leading-relaxed;
}
</style>
