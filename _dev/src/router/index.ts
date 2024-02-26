import Vue from 'vue';
import VueRouter, {NavigationGuard, RouteConfig} from 'vue-router';
import CatalogTabPages from '@/components/catalog/pages';
import GettersTypesApp from '@/store/modules/app/getters-types';
import ActionsTypesOnboarding from '@/store/modules/onboarding/actions-types';
import GettersTypesOnboarding from '@/store/modules/onboarding/getters-types';
import store from '@/store';
import Configuration from '@/views/configuration-tab.vue';
import BillingTab from '@/views/billing-tab.vue';
import CatalogTab from '@/views/catalog-tab.vue';
import Debug from '@/views/debug-page.vue';
import IntegrateTab from '@/views/integrate-tab.vue';
import LandingPage from '@/views/landing-page.vue';
import Help from '@/views/help.vue';

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

const initialPath = async (to, from, next) => {
  await store.dispatch(`onboarding/${ActionsTypesOnboarding.WARMUP_STORE}`);
  if (from.path === '/'
    && !store.getters[`onboarding/${GettersTypesOnboarding.IS_USER_ONBOARDED}`]
  ) {
    next({name: 'landing-page'});
  } else {
    next({name: 'Configuration'});
  }
};

const routes: RouteConfig[] = [
  {
    path: '/landing-page',
    name: 'landing-page',
    component: LandingPage,
  },
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
        name: CatalogTabPages.nonCompliantProductsReport,
        component: CatalogTab,
        beforeEnter: billingNavigationGuard,
      },
      {
        path: 'reporting/synchonization',
        name: CatalogTabPages.disapprovedProductsReport,
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
    beforeEnter: initialPath,
  },
  {
    path: '/integrate',
    name: 'Integrate',
    component: IntegrateTab,
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
