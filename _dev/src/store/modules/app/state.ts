import {ISubscription} from '@prestashopcorp/billing-cdc/dist/@types/Subscription';

export type State = {
  links: {
    coreProductsPageUrl?: string,
    coreModuleActionUrl?: string,
  },
  hooks: HooksStatuses;
  billing: {
    subscription?: ISubscription;
  }
}

export type HooksStatuses = {[key: string]: boolean};

export const state: State = {
  hooks: {},
  billing: {
    subscription: window.psBillingSubscription,
  },
  links: {
    coreProductsPageUrl: window.psFacebookProductsUrl,
    // The action GET_MODULES_VERSIONS may retrieve the data, but it is relatively recent.
    // The psFacebookUpgradeRoute has been existing for a longer time, making it more reliable.
    coreModuleActionUrl: window.psCloudSyncVersionCheck?.psFacebookUpgradeRoute
      .replace('ps_eventbus', '{module}')
      .replace('upgrade', '{action}'),
  },
};
