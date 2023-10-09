import Vue from 'vue';
import Vuex from 'vuex';
import app from './modules/app';
import catalog from './modules/catalog';
import context from './modules/context';
import onboarding from './modules/onboarding';
import {State as AppState} from './modules/app/state';
import {State as CatalogState} from './modules/catalog/state';
import {State as OnboardingState} from './modules/onboarding/state';

Vue.use(Vuex);

export interface FullState {
  app: AppState,
  catalog: CatalogState,
  onboarding: OnboardingState,
}

export default new Vuex.Store<FullState>({
  modules: {
    app,
    catalog,
    context,
    onboarding,
  },
});
