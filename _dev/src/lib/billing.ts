import {EVENT_HOOK_TYPE} from '@prestashopcorp/billing-cdc/dist/constants/EventHookType';
import {State as AppState} from '@/store/modules/app/state';

export const billingUpdateCallback = (
  psBilling: unknown,
  state: AppState,
) => (type: EVENT_HOOK_TYPE, data: any) => {
  switch (type) {
    case psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_CREATED:
    case psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_UPDATED:
    case psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_CANCELLED:
    case psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_REACTIVATED:
      if (data?.subscription) {
        state.billing.subscription = data.subscription;
      }
      break;
    default:
      break;
  }
};

export default {
  billingUpdateCallback,
};
