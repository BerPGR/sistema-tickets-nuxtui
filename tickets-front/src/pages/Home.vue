<template>
  <div class="min-h-screen w-full container mx-auto py-10">
    <span>Bem vindo, <b>{{ store.user.name }}</b></span>
    <UCard class="mt-10">
      <template #header>
        <h1 class="text-2xl font-semibold">Meus Tickets</h1>
      </template>
      <UTable :data="ticketsData" :columns="columns"/>
    </UCard>
  </div>
</template>

<script setup lang='ts'>
  import { useAuthStore } from '@/stores/useAuth.ts'
  import { ref, onMounted, h, resolveComponent } from 'vue'
  import { http } from '../api/api.ts'
  import { useRouter } from 'vue-router'
  import useTickets from '@/composables/useTickets.ts'
  import type { Tickets } from '@/types/types.ts'
  import type { TableColumn } from '@nuxt/ui'
  import type { Row } from '@tanstack/vue-table'

  const UBadge = resolveComponent('UBadge')
  const UButton = resolveComponent('UButton')
  const UDropdownMenu = resolveComponent('UDropdownMenu')
  
  const message = ref<string>('')
  
  const store = useAuthStore()
  const router = useRouter()
  const { getUserTickets } = useTickets()

  const ticketsData = ref<Tickets[]>([])

  const columns: TableColumn<Tickets>[] = [
    {
      accessorKey: 'id',
      header: 'ID',
      cell: ({ row }) => `#${row.getValue('id')}`
    },
    {
      accessorKey: 'title',
      header: 'Título',
      cell: ({ row }) => row.getValue('title')
    },
    {
      accessorKey: 'client',
      header: 'Cliente',
      cell: ({ row }) => row.getValue('title')
    },
    {
     accessorKey: 'status',
     header: 'Status',
     cell: ({ row }) => row.getValue('status')
    },
    {
      accessorKey: 'due_date',
      header: 'Deadline',
      cell: ({ row }) => {
        return new Date(row.getValue('due_date')).toLocaleString('pt-BR', {
          day: 'numeric',
          month: 'numeric',
          year: '2-digit'
        });
      }
    },
    {
      accessorKey: 'created_at',
      header: 'Criado em',
      cell: ({ row }) => {
        return new Date(row.getValue('created_at')).toLocaleString('pt-BR', {
          day: 'numeric',
          month: 'numeric',
          year: '2-digit'
        }); 
      }
    },
    {
      accessorKey: 'owner',
      header: 'Criado por',
      cell: ({ row }) => row.getValue('owner')
    },
    {
      accessorKey: 'team',
      header: 'Grupo responsável',
      cell: ({ row }) => row.getValue('team')
    },
    {
      accessorKey: 'responsable',
      header: 'Usuário responsável',
      cell: ({ row }) => row.getValue('responsable')
    },
    {
      accessorKey: 'priority',
      header: 'Prioridade',
      cell: ({ row }) => {
        const color = {
          ALTA: 'error' as const,
          MEDIA: 'warning' as const,
          BAIXA: 'success' as const
        }[row.getValue('priority') as string]

        return h(UBadge, { class: 'Capitalize', variant: 'subtle', color }, () => row.getValue('priority'))
      }
    },
    {
      header: 'Ações',
      id: 'actions',
      meta: {
        class: {
          td: 'text-right'
        }
      },
      cell: ({ row }) => {
        return h(
          UDropdownMenu,
          {
            content: {
              align: 'end'
            },
            items: getRowItems(row)
          },
          () => h(UButton, {
            icon: 'i-lucide-ellipsis-vertical',
            color: 'neutral',
            variant: 'ghost',
            'aria-lable': 'Actions Dropdown'
          })
        )
      }
    }
  ] 

  function getRowItems(row: Row<Tickets>) {
    return [
      {
        type: 'label',
        label: 'Ações'
      },
      {
        label: 'Acessar ticket',
        onSelect() {
         const id = row.original.id
         router.push(`/ticket/${id}`)
        }
      }
    ]
  }

  onMounted(async () => {
  try {
      if (store.user === null) {
        router.replace('/auth')
        return
      }
      const response = await http.get<Tickets[]>(`/api/users/${store.user.id}/tickets`) 

      if (response.status === 200) {
        const data = response.data
        ticketsData.value = data
      }
  } catch (error) {
      console.error("Erro na requisição:", error)
      message.value = "Erro ao carregar"
  }  })
</script>
