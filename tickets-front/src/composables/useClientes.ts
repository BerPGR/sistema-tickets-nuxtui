import { http } from '@/api/api.ts'

export default function useClientes() {
  const getClientes = async () => {
    const response = await http.get('/api/clients')
    let clients = []
    if (response.status === 200) {
      const data = response.data
      clients = data.map((c) => {
        return {
          label: c.name,
          value: c.id
        }
      })
    }
    
    return clients
  }

  return { getClientes }
}
