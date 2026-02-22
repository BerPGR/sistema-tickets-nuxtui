import { createRouter, createMemoryHistory } from 'vue-router'
import Home from '@/pages/Home.vue'
import View from '@/pages/View.vue'
import Auth from '@/pages/Auth.vue'
import AddTicket from '@/pages/AddTicket.vue'
const routes = [
  { path: "/", component: Home },
  { path: "/view", component: View },
  { path: "/auth", component: Auth },
  { path: "/add-ticket", component: AddTicket }
]

export const router = createRouter({
  history: createMemoryHistory(),
  routes
})
