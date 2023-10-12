import Vue from 'vue';
import VueRouter, {RouteConfig} from 'vue-router';
import Configuration from '../views/configuration.vue';
import CatalogTab from '../views/catalog-tab.vue';
import Debug from '../views/debug-page.vue';
import Integrate from '../views/integrate.vue';
import BillingTab from '../views/billing-tab.vue';
import Help from '../views/help.vue';
import CatalogTabPages from '../components/catalog/pages';

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
    component: CatalogTab,
    redirect: {name: CatalogTabPages.summary},
    children: [
      {
        path: 'summary',
        name: CatalogTabPages.summary,
        component: CatalogTab,
      },
      {
        path: 'category-matching/view',
        name: CatalogTabPages.categoryMatchingView,
        component: CatalogTab,
      },
      {
        path: 'category-matching/edit',
        name: CatalogTabPages.categoryMatchingEdit,
        component: CatalogTab,
      },
      {
        path: 'reporting/verification',
        name: CatalogTabPages.prevalidationDetails,
        component: CatalogTab,
      },
      {
        path: 'reporting/synchonization',
        name: CatalogTabPages.reportDetails,
        component: CatalogTab,
      },
    ],
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
