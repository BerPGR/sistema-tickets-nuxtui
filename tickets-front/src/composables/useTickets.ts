import { http } from '@/api/api.ts'
import { useAuthStore } from '@/stores/useAuth.ts'
import type { Tickets } from '@/types/types.ts'

export default function useTickets() {
  const save = async (data: Object) => {
    const response = await http.post('/api/tickets', data);
 
    if (response.status === 201) {
      return response.data
    }
  }

  const getTicket = async (id: number) => {
    const response = await http.get<Tickets>(`/api/tickets/${id}`)
  
    if (response.status === 200) {
      const data = response.data
      return data
    }
    return {}
  }

  return { save, getTicket }
}
