import { http } from '@/api/api.ts'
import { useAuthStore } from '@/stores/useAuth.ts'

export default function useTickets() {
  const save = async (data: Object) => {
    const response = await http.post('/api/tickets', data);
 
    if (response.status === 201) {
      return response.data
    }
  }

  return { save }
}
