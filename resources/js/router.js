import Vue from 'vue';
import VueRouter from 'vue-router';
import Dashboard from '../components/Dashboard.vue';
import History from '../components/History.vue';

Vue.use(VueRouter);

const routes = [
  {
    path: '/history/:email',
    name: 'History',
    component: History,
    props: true
  },
  {
    path: '/:email?',
    name: 'Dashboard',
    component: Dashboard,
    props: (route) => ({
      userEmail: route.params.email || 'ivan@example.com'
    })
  }
];

const router = new VueRouter({
  mode: 'history',
  routes
});

export default router;