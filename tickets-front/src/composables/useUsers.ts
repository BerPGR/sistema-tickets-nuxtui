import { http } from '@/api/api.ts'
import type { SelectItem } from '@nuxt/ui'

export default function useUsers() {
  const getAllUsers = async () => {
    const { data } = await http.get('/api/users')
    let sUsers = []

    if (data) {
      sUsers = data.map((u) => {
        return {
          label: u.name,
          value: u.id,
          team_id: u.team_id
        }
      })
    }

    return sUsers
  }

  return { getAllUsers }
}
