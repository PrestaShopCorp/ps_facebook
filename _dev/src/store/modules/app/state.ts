import {ISubscription} from '@prestashopcorp/billing-cdc';

export type State = {
  links: {
    coreProductsPageUrl?: string,
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
  }
};
