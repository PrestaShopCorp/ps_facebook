import GettersTypes from './getters-types';
import {HooksStatuses, State as LocalState} from './state';

export default {
  [GettersTypes.GET_HOOKS_STATUS](state: LocalState): HooksStatuses {
    return state.hooks;
  },
};
