import Vue from 'vue';
import VueRouter, {RouteConfig} from 'vue-router';
import Configuration from '../views/configuration.vue';
import Catalog from '../views/catalog.vue';
import Debug from '../views/debug-page.vue';
import Integrate from '../views/integrate.vue';
import BillingTab from '../views/billing-tab.vue';
import Help from '../views/help.vue';

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
    path: '/help',
    name: 'help',
    component: Help,
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
  {
    path: '/billing',
    name: 'Billing',
    component: BillingTab,
  },
  {
    path: '/debug',
    name: 'Debug',
    component: Debug,
  },
];

const router = new VueRouter({
  routes,
});

export default router;
