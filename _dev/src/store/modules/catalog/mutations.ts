import {RequestState} from '@/store/modules/catalog/types';
import MutationsTypes from './mutations-types';
import {
  State as LocalState,
  ProductFeedReport,
  CategoryMatchingStatus,
  CatalogRequests,
} from './state';

export default {
  [MutationsTypes.SET_CATALOG_PAGE_ENABLED](state: LocalState) {
    state.enabledFeature = true;
  },
  [MutationsTypes.SET_SYNCHRONIZATION_ACTIVE](state: LocalState, enabled: boolean) {
    state.exportOn = enabled;
  },
  [MutationsTypes.SET_SYNCHRONIZATION_SUMMARY](
    state: LocalState,
    payload: Partial<ProductFeedReport>,
  ) {
    state.report = {
      ...state.report,
      ...payload,
    };
  },
  [MutationsTypes.SET_CATEGORY_MATCHING_SUMMARY](
    state: LocalState,
    payload: CategoryMatchingStatus,
  ) {
    state.categoryMatching = payload;
  },

  [MutationsTypes.SET_REQUEST_STATE](
    state: LocalState,
    payload: {
      request: keyof CatalogRequests,
      newState: RequestState,
    },
  ) {
    state.requests[payload.request] = payload.newState;
  },
};
