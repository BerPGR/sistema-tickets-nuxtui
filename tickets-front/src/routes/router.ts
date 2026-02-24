import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/pages/Home.vue'
import View from '@/pages/View.vue'
import Auth from '@/pages/Auth.vue'
import AddTicket from '@/pages/AddTicket.vue'
import Ticket from '@/pages/Ticket.vue'

const routes = [
  { path: "/", component: Home },
  { path: "/view", component: View },
  { path: "/auth", component: Auth },
  { path: "/add-ticket", component: AddTicket },
  { path: "/ticket/:id", component: Ticket}
]

export const router = createRouter({
  history: createWebHistory(),
  routes
})
