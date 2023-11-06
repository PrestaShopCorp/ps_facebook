import Vue from 'vue';
import Vuex from 'vuex';
import app from './modules/app';
import catalog from './modules/catalog';
import context from './modules/context';
import onboarding from './modules/onboarding';
import {FullState} from '@/store/types';

Vue.use(Vuex);

export default new Vuex.Store<FullState>({
  modules: {
    app,
    catalog,
    context,
    onboarding,
  },
});
