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
            <span class="font-semibold text-secondary">Prioridade</span>
            <label>{{ticket.priority}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary">Aberto em</span>
            <label>{{formattedDate(ticket.created_at)}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary">Deadline</span>
            <label>{{formattedDate(ticket.due_date)}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary">Status</span>
            <label>{{ticket.status}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary">Aberto por</span>
            <label>{{ticket.owner}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary">Grupo responsável</span>
            <label>{{ticket.team}}</label>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-secondary">Usuário responsável</span>
            <label>{{ticket.responsable}}</label>
          </div>

        </div>
      </template>
    </UCard>
    
    <UCard class="w-1/3 shadow-xl">
      <div class="flex flex-col gap-4">
        <UButton size="xl" label="Comentar" color="primary"/> 
        <UButton size="xl" label="Devolver" color="secondary"/> 
        <UButton size="xl" label="Atualizar" color="warning"/> 
        <UButton size="xl" label="Finalizar" color="error"/> 
        <UButton size="xl" label="Lista" color="info"/> 
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
