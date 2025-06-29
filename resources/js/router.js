import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from './components/Dashboard.vue'
import History from './components/History.vue'

const routes = [
  {
    path: '/:email?',
    name: 'Dashboard',
    component: Dashboard,
    props: true
  },
  {
    path: '/history/:email',
    name: 'History',
    component: History,
    props: true
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router