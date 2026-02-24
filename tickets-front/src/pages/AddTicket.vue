<template>
  <UContainer class="py-4 min-h-[calc(100dvh-12rem)] w-full">
  <UCard class="p-3 shadow-xl" :ui="{
    footer: 'flex justify-end'
  }">
      <template #header>
        <h1 class="text-2xl font-semibold">Novo Ticket</h1>
      </template>

      <UForm :schema="schema" :state="state" class="space-y-4" @submit="onSubmit">

        <div class="grid md:grid-cols-3 gap-10">
          <UFormField class="w-full" label="Cliente">
            <USelect size="xl" class="w-full" :items="clientItems" v-model="state.client_id"/>
          </UFormField>
          <UFormField class="w-full" label="Grupo responsavel">
            <USelect size="xl" class="w-full" :items="teamItems" v-model="state.team_id"/>
          </UFormField>
          <UFormField class='w-full' label="Atribuir ticket para">
            <USelect class="w-full" size="xl" v-model='state.user_id' :disabled="!state.team_id" :items="filteredUserItems"/>
          </UFormField>
          <UFormField class="w-full" label="Prioridade">
            <USelect class="w-full" size='xl' v-model='state.priority' :items="itemsPriority"/>
          </UFormField>
          <UFormField class="w-full" label="Prazo de entrega">
            <UPopover>
              <UButton size='xl' color="neutral" variant="subtle" icon='i-lucide-calendar'>
              {{ state.dueDate ? df.format(modelValue.toDate(getLocalTimeZone())) : 'Selecione o prazo' }}
              </UButton>
              <template #content>
                <UCalendar v-model="modelValue" class="p-2" />
              </template>
            </UPopover>
          </UFormField>
        </div>

        <UModal v-model:open="open" class="mt-4" title="Adicionar tag" :ui="{ footer: 'justify-end' }" :close="{ color: 'primary', variant: 'subtle', class: 'rounded-full'}">
          <UButton icon="i-mdi-add" label="Adicionar tags" size='md' class='rounded-full border-2 border-dashed cursor-pointer' @click="open = true" variant="ghost"/>
          <template #body>
            <UInput placeholder="Digite uma tag" class="w-full" size="xl" v-model="modalTag" />
          </template>
          <template #footer>
            <UButton label="Adicionar" size="xl" @click="toggleTag(modalTag)" class='cursor-pointer'/>
          </template>
        </UModal>
        <div class="flex items-center wrap gap-4">
          <UBadge class="cursor-pointer" variant="subtle" v-for="(tag, i) in state.tags" :key="i" :label="tag" @click="toggleTag(tag)" />
        </div>
        
        <USeparator/>

        <UFormField label="Titulo">
          <UInput size="xl" class="w-full" placeholder="Titulo do ticket" v-model="state.title"/>
        </UFormField>
        <UFormField label="Descrição">
          <UEditor v-slot="{ editor }" content-type="html" placeholder="Descrição do ticket..." v-model='state.description' class='min-h-50 border border-gray-300 rounded-md'>
            <UEditorToolbar :editor="editor" :items='items' />
            <UEditorDragHandle :editor="editor" icon="i-lucide-move"/>
          </UEditor>
        </UFormField>

        <USeparator class="my-4" />

        <UButton class="flex text-lg font-semibold cursor-pointer mt-4" size="xl" type="submit" color="secondary" label="Criar Ticket" />
      </UForm>
    </UCard>
  </UContainer>
</template>

<script setup lang="ts">
import type { EditorToolbarItem } from '@nuxt/ui'
import { reactive, shallowRef, ref, watch, onMounted } from 'vue'
import * as z from 'zod'
import { CalendarDate, DateFormatter, getLocalTimeZone } from '@internationalized/date'
import type { FormError, FormErrorEvent, FormSubmitEvent, SelectItem } from '@nuxt/ui'
import { useRouter } from 'vue-router'

import useUsers from '@/composables/useUsers.ts'
import useTags from '@/composables/useTags.ts'
import useTickets from '@/composables/useTickets.ts'
import useTeams from '@/composables/useTeams.ts'
import useClientes from '@/composables/useClientes.ts'
import { useAuthStore } from '@/stores/useAuth.ts'

const { getTeams } = useTeams() 
const { getAllUsers } = useUsers()
const { save } = useTickets()
const { getClientes } = useClientes()
const { insertTags } = useTags()
const useAuth = useAuthStore()
const router = useRouter()

const userItems = ref<SelectItem[] | null>(null)
const clientItems = ref<SelectItem[] | null>(null)
const teamItems = ref<SelectItem[] | null>(null)

const open = ref<boolean>(false)
const allUsers = ref<any[]>([]) 
const filteredUserItems = ref<any[]>([])
const modalTag = ref('')
const modelValue = shallowRef(new CalendarDate(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate()))

const itemsPriority = ref<SelectItem[]>([
  {
    label: 'Alta',
    value: 'ALTA'
  },
  {
    label: 'Média',
    value: 'MEDIA'
  },
  {
    label: 'Baixa',
    value: 'BAIXA'
  }
])

const schema = z.object({
  title: z.string().min(8, 'Titulo deve conter ao menos 8 caracteres!'),
  description: z.string('Descrição não pode ser nula!'),
  dueDate: z.any(),
  priority: z.string(),
  user_id: z.number(),
  team_id: z.number(),
  client_id: z.number(),
  tags: z.array(z.string())
})

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  title: undefined,
  description: undefined,
  dueDate: undefined,
  priority: undefined,
  user_id: undefined,
  client_id: undefined,
  team_id: undefined,
  tags: []
})

onMounted(async () => {
  const [teams, users, clients] = await Promise.all([
    getTeams(),
    getAllUsers(),
    getClientes()
  ])

  teamItems.value = teams
  allUsers.value = users
  clientItems.value = clients
  filteredUserItems.value = [] 
})

const toggleTag = (tagValue: string) => {
  if (!tagValue || tagValue.trim() === '') { 
    open.value = false
    return
  }
  const index = state.tags.indexOf(tagValue)

  if (index !== -1) {
    state.tags.splice(index, 1)
  } else {
    state.tags.push(tagValue)
  }

  modalTag.value = ''
  open.value = false
}

  
const onSubmit = async (event: FormSubmitEvent<Schema>) => {
  const {tags, ...rest} = event.data

  const saved = await save({...rest, owner_id: useAuth.user.id})

  if (tags.length > 0) {
    await insertTags(tags, saved)
  }

  router.replace('/')
}

const df = new DateFormatter('pt-BR', {
  dateStyle: 'long'
})

const formatDate = (date: any) => {
  if (!date) return ''
  const d = date.toDate(getLocalTimeZone())
  return d.toISOString().split('T')[0]
}

watch(modelValue, (newValue) => {
  state.dueDate = formatDate(newValue) 
})

watch(() => state.team_id, (newTeamId) => {
  if (!newTeamId) {
    filteredUserItems.value = []
    state.user_id = undefined
    return
  }

  filteredUserItems.value = allUsers.value.filter(user => user.team_id === newTeamId)
})

const items: EditorToolbarItem[] = [
  { kind: 'mark', mark: 'bold', icon: 'i-lucide-bold' },
  { kind: 'mark', mark: 'italic', icon: 'i-lucide-italic' },
  { kind: 'heading', level: 1, icon: 'i-lucide-heading-1' },
  { kind: 'heading', level: 2, icon: 'i-lucide-heading-2' },
  { kind: 'textAlign', align: 'left', icon: 'i-lucide-align-left' },
  { kind: 'textAlign', align: 'center', icon: 'i-lucide-align-center' },
  { kind: 'bulletList', icon: 'i-lucide-list' },
  { kind: 'orderedList', icon: 'i-lucide-list-ordered' },
  { kind: 'blockquote', icon: 'i-lucide-quote' },
  { kind: 'link', icon: 'i-lucide-link' }
]
</script>
