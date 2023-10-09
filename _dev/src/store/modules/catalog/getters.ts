import GettersTypes from './getters-types';
import {State as LocalState} from './state';

export default {
  [GettersTypes.GET_CATALOG_PAGE_ENABLED](state: LocalState): boolean {
    return state.enabledFeature;
  },
  [GettersTypes.GET_SYNCHRONIZATION_ENABLED](state: LocalState): boolean {
    return state.enabledFeature && !!state.exportOn;
  },
};
