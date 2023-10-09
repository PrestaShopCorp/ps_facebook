import MutationsTypes from './mutations-types';
import {
  State as LocalState,
  ProductFeedReport,
  CategoryMatchingStatus,
} from './state';

export default {
  [MutationsTypes.SET_CATALOG_PAGE_ENABLED](state: LocalState) {
    state.enabledFeature = true;
  },
  [MutationsTypes.SET_SYNCHRONIZATION_ACTIVE](state: LocalState, enabled: boolean) {
    state.exportOn = enabled;
  },
  [MutationsTypes.SET_SYNCHRONIZATION_SUMMARY](state: LocalState, payload: ProductFeedReport) {
    state.report = payload;
  },
  [MutationsTypes.SET_CATEGORY_MATCHING_SUMMARY](
    state: LocalState,
    payload: CategoryMatchingStatus,
  ) {
    state.categoryMatching = payload;
  },
};
