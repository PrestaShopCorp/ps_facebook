import Vue from 'vue';
import Vuex from 'vuex';
import {StoreState} from './types';
import context from './modules/context';
import onboarding from './modules/onboarding';

Vue.use(Vuex);

export default new Vuex.Store<StoreState>({
  modules: {
    context,
    onboarding,
  },
});
