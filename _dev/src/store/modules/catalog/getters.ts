import GettersTypes from './getters-types';
import {CategoryMatchingStatus, State as LocalState, ProductFeedReport} from './state';

export default {
  [GettersTypes.GET_CATALOG_PAGE_ENABLED](state: LocalState): boolean {
    return state.enabledFeature;
  },
  [GettersTypes.GET_SYNCHRONIZATION_ACTIVE](state: LocalState): boolean {
    return state.enabledFeature && !!state.exportOn;
  },
  [GettersTypes.GET_SYNCHRONIZATION_SUMMARY](state: LocalState): ProductFeedReport|undefined {
    return state.report;
  },
  [GettersTypes.GET_CATEGORY_MATCHING_SUMMARY](
    state: LocalState,
  ): CategoryMatchingStatus {
    return state.categoryMatching;
  },
};
