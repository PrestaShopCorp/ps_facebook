import {EVENT_HOOK_TYPE} from '@prestashopcorp/billing-cdc/dist/constants/EventHookType';
import billing from '@prestashopcorp/billing-cdc';
import {State as AppState} from '@/store/modules/app/state';

const defaultPlanId = 'ps_facebook-standard-EUR-Monthly';

// Implementation of a custom initialization method so we can get a better control (ie on Modal)
export const initialize = (psBilling: typeof billing,
  billingContext: {
    [key: string]: unknown;
  },
  domComponentSelector: string,
  domModalSelector: string,
  onEventHookCallback: (type: EVENT_HOOK_TYPE, data: unknown) => void,
  hideInvoiceList: boolean = true,
) => {
  let currentModal;

  const onEventHook = (
    onEventHookCallback: (type: EVENT_HOOK_TYPE, data: unknown) => void,
  ) => (type: EVENT_HOOK_TYPE, data: unknown) => {
    // Event hook listener
    switch (type) {
      case psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_CREATED:
        showBillingWrapper();
        break;
      case psBilling.EVENT_HOOK_TYPE.SUBSCRIPTION_UPDATED:
        showBillingWrapper();
        break;
      default:
        break;
    }

    onEventHookCallback(type, data);
  };

  const customer = new psBilling.CustomerComponent({
    context: billingContext,
    hideInvoiceList,
    onOpenModal,
    onEventHook: onEventHook(onEventHookCallback),
    onOpenFunnel,
  });
  customer.render(domComponentSelector);

  // Modal open / close management
  async function onCloseModal(data) {
    await Promise.all([currentModal.close(), updateCustomerProps(data)]);
  }

  function onOpenModal(type, data) {
    currentModal = new psBilling.ModalContainerComponent({
      type,
      context: {
        ...billingContext,
        ...data,
      },
      onCloseModal,
      onEventHook: onEventHook(onEventHookCallback),
    });
    currentModal.render(domModalSelector);
  }

  function updateCustomerProps(data) {
    return customer.updateProps({
      context: {
        ...billingContext,
        ...data,
      },
    });
  }

  function showPlanPresenter() {
    const planWrapper = document.getElementById('billing-plan-presenter');
    if (planWrapper) {
      planWrapper.classList.remove('hide');
    }
    const billingWrapper = document.getElementById('ps-billing-wrapper');
    if (billingWrapper) {
      billingWrapper.classList.add('hide');
    }
  }

  function showBillingWrapper() {
    const planWrapper = document.getElementById('billing-plan-presenter');
    if (planWrapper) {
      planWrapper.classList.add('hide');
    }
    const billingWrapper = document.getElementById('ps-billing-wrapper');
    if (billingWrapper) {
      billingWrapper.classList.remove('hide');
    }
  }

  function onOpenFunnel({subscription}) {
    showPlanPresenter();
  }

  // Open the checkout full screen modal
  function openCheckout(modalType = psBilling.MODAL_TYPE.SUBSCRIPTION_FUNNEL) {
    const offerSelection = {offerSelection: {offerPricingId: defaultPlanId}};
    onOpenModal(
      modalType,
      offerSelection,
    );
  }

  return {
    openCheckout,
  };
};

export const billingUpdateCallback = (
  psBilling: typeof billing,
  state: AppState,
) => (type: EVENT_HOOK_TYPE, data: any) => {
  console.log('billingUpdateCallback', type);
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
