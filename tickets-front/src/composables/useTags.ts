import { http } from '@/api/api.ts'

export default function useTags() {
  const insertTags = async (tags: Object, savedTicketId: number) => {
    const response = await http.post(`/api/tickets/${savedTicketId}/tags`, tags)
    
    if (response.status === 201) {
      return true
    }

    return false
  }

  return { insertTags }
}
