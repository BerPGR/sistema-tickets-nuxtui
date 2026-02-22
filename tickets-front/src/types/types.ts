export type Tickets = {
  id: number | string,
  owner: string,
  responsable: string,
  created_at: Date,
  due_date: Date,
  priority: 'ALTA' | 'MEDIA' | 'BAIXA'
  client: string,
  team: string,
  title: string
} 
