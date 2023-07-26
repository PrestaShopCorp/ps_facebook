import MutationsTypes from './mutations-types';
import {
  HooksStatuses,
  State as LocalState,
} from './state';

export default {
  [MutationsTypes.SET_HOOKS_STATUS](state: LocalState, response: HooksStatuses) {
    state.hooks = response;
  },
};
