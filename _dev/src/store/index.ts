import Vue from 'vue';
import Vuex from 'vuex';
import {StoreState} from './types';
import context from './modules/context';

Vue.use(Vuex);

export default new Vuex.Store<StoreState>({
  state: {},
  mutations: {},
  actions: {},
  modules: {
    context,
  },
});
