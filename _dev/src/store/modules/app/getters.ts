import GettersTypes from './getters-types';
import {HooksStatuses, State as LocalState} from './state';

export default {
  [GettersTypes.GET_HOOKS_STATUS](state: LocalState): HooksStatuses {
    return state.hooks;
  },
  [GettersTypes.GET_BILLING_SUBSCRIPTION_ACTIVE](state: LocalState) {
    return state.billing.subscription
      // Using the type from billing-cdc prevents the module to work
      && state.billing.subscription.status !== 'cancelled';
  },
  [GettersTypes.GET_BILLING_SUBSCRIPTION_EXPIRING](state: LocalState) {
    return state.billing.subscription
      // Using the type from billing-cdc prevents the module to work
      && state.billing.subscription.status === 'non_renewing';
  },
};
