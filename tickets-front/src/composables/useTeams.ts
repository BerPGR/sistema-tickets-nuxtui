import { http } from '@/api/api.ts'

export default function useTeams() {
  const getTeams = async () => {
    const response = await http.get('/api/teams')
    let teams = []
    if (response.status === 200) {
      const data = response.data
      teams = data.map(t => {
        return {
          label: t.name,
          value: t.id
        }
      })
    }
    return teams
  }

  return {
    getTeams
  }
}
