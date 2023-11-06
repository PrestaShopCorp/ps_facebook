import Vue from 'vue';
import VueRouter, {NavigationGuard, RouteConfig} from 'vue-router';
import Configuration from '../views/configuration.vue';
import CatalogTab from '../views/catalog-tab.vue';
import Debug from '../views/debug-page.vue';
import Integrate from '../views/integrate.vue';
import BillingTab from '../views/billing-tab.vue';
import Help from '../views/help.vue';
import CatalogTabPages from '../components/catalog/pages';
import GettersTypesApp from '@/store/modules/app/getters-types';
import store from '@/store';

Vue.use(VueRouter);

const billingNavigationGuard: NavigationGuard = (to, from, next) => {
  if (!window.psAccountShopId) {
    return next({name: 'Configuration'});
  }

  if (!store.getters[`app/${GettersTypesApp.GET_BILLING_SUBSCRIPTION_ACTIVE}`]) {
    return next({name: 'Configuration'});
  }

  return next();
};

const routes: RouteConfig[] = [
  {
    path: '/configuration',
    name: 'Configuration',
    component: Configuration,
  },
  {
    path: '/catalog',
    name: 'Catalog',
    component: CatalogTab,
    beforeEnter: billingNavigationGuard,
    redirect: {name: CatalogTabPages.summary},
    children: [
      {
        path: 'summary',
        name: CatalogTabPages.summary,
        component: CatalogTab,
        beforeEnter: billingNavigationGuard,
      },
      {
        path: 'category-matching/view',
        name: CatalogTabPages.categoryMatchingView,
        component: CatalogTab,
        beforeEnter: billingNavigationGuard,
      },
      {
        path: 'category-matching/edit',
        name: CatalogTabPages.categoryMatchingEdit,
        component: CatalogTab,
        beforeEnter: billingNavigationGuard,
      },
      {
        path: 'reporting/verification',
        name: CatalogTabPages.prevalidationDetails,
        component: CatalogTab,
        beforeEnter: billingNavigationGuard,
      },
      {
        path: 'reporting/synchonization',
        name: CatalogTabPages.reportDetails,
        component: CatalogTab,
        beforeEnter: billingNavigationGuard,
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
    beforeEnter: billingNavigationGuard,
  },
  {
    path: '/billing',
    name: 'Billing',
    component: BillingTab,
    beforeEnter: billingNavigationGuard,
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
