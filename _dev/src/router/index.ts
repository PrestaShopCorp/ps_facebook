import Vue from 'vue';
import VueRouter, {RouteConfig} from 'vue-router';
import Configuration from '../views/configuration.vue';
import Catalog from '../views/catalog.vue';
import Integrate from '../views/integrate.vue';

Vue.use(VueRouter);

const routes: Array<RouteConfig> = [
  {
    path: '/configuration',
    name: 'Configuration',
    component: Configuration,
  },
  {
    path: '/catalog',
    name: 'Catalog',
    component: Catalog,
  },
  {
    path: '/',
    redirect: '/configuration',
  },
  {
    path: '/integrate',
    name: 'Integrate',
    component: Integrate,
  },
];

const router = new VueRouter({
  routes,
});

export default router;
