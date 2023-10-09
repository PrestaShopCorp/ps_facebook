import Vue from 'vue';
import Vuex from 'vuex';
import {StoreState} from './types';
import app from './modules/app';
import catalog from './modules/catalog';
import context from './modules/context';
import onboarding from './modules/onboarding';

Vue.use(Vuex);

export default new Vuex.Store<StoreState>({
  modules: {
    app,
    catalog,
    context,
    onboarding,
  },
});
