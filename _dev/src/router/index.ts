import Vue from 'vue';
import VueRouter, {RouteConfig} from 'vue-router';
import Configuration from '../views/Configuration.vue';

Vue.use(VueRouter);

const routes: Array<RouteConfig> = [
  {
    path: '/configuration',
    name: 'Configuration',
    component: Configuration,
  },
];

const router = new VueRouter({
  routes,
});

export default router;
