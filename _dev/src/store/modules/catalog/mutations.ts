import MutationsTypes from './mutations-types';
import {
  State as LocalState,
} from './state';

export default {
  [MutationsTypes.SET_SYNCHRONIZATION_SUMMARY](state: LocalState, response: any) {
    // state.externalBusinessID = response;
  },
};
