import GettersTypes from './getters-types';
import {State as LocalState, OnboardingContext} from './state';

export default {
  [GettersTypes.GET_EXTERNAL_BUSINESS_ID](state: LocalState): string|null {
    return state.externalBusinessID;
  },

  [GettersTypes.GET_ONBOARDING_STATE](state: LocalState): OnboardingContext {
    return state.onboarded;
  },

  [GettersTypes.IS_USER_ONBOARDED](state: LocalState): boolean {
    return !!state.onboarded.user?.email;
  },
};
