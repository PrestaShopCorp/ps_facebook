import MutationsTypes from './mutations-types';
import {
  State as LocalState, OnboardingContext,
} from './state';

export default {
  [MutationsTypes.SET_EXTERNAL_BUSINESS_ID](state: LocalState, response: string) {
    state.externalBusinessID = response;
  },

  [MutationsTypes.SET_ONBOARDED_CONTEXT](
    state: LocalState,
    newState: OnboardingContext,
  ) {
    state.onboarded = newState;
  },
  [MutationsTypes.UPDATE_PIXEL_STATUS](state: LocalState, newStatus: boolean) {
    if (state.onboarded) {
      state.onboarded.pixel.isActive = newStatus;
    }
  },
};
