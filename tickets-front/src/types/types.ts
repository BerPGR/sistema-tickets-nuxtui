export type Tickets = {
  id: number | string,
  owner: string,
  responsable: string,
  created_at: Date,
  due_date: Date,
  priority: 'ALTA' | 'MEDIA' | 'BAIXA',
  status: string,
  client: string,
  team: string,
  title: string,
  tags: string | null
} 
