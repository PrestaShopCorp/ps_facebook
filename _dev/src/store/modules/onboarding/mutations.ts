import MutationsTypes from './mutations-types';
import {
  State as LocalState, OnboardingContext,
} from './state';

export default {
  [MutationsTypes.SET_EXTERNAL_BUSINESS_ID](state: LocalState, response: string) {
    state.externalBusinessID = response;
  },

  [MutationsTypes.SET_ONBOARDED_APP]<T extends keyof OnboardingContext>(state: LocalState, payload: {
    app: T,
    newState: OnboardingContext[T],
  }) {
    state.onboarded[payload.app] = payload.newState;
  },
};
