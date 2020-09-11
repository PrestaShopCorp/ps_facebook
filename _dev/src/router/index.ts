import Vue from 'vue';
import VueRouter, {RouteConfig} from 'vue-router';
import Onboarding from '../views/Onboarding.vue';

Vue.use(VueRouter);

const routes: Array<RouteConfig> = [
  {
    path: '/onboarding',
    name: 'Onboarding',
    component: Onboarding,
  },
];

const router = new VueRouter({
  routes,
});

export default router;
