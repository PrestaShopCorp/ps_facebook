import Vue, {VNode} from 'vue';
import {IContextAuthentication, IContextBase, ISubscription} from '@prestashopcorp/billing-cdc';

declare global {
  // namespace JSX {
  //   // tslint:disable no-empty-interface
  //   interface Element extends VNode {}
  //   // tslint:disable no-empty-interface
  //   interface ElementClass extends Vue {}

    interface Window {
      contextPsAccounts: any;
      psAccountShopId: string|null;
      contextPsEventbus: any;
      psBillingContext: IContextBase<IContextAuthentication>;
      psBillingSubscription?: ISubscription;
      psBilling: unknown;
      i18nSettings: any;

      psFacebookRouteToShopApi?: string;
      psFacebookProductsUrl?: string;

      psFacebookPixelActivationRoute: string;
      psFacebookFbeOnboardingSaveRoute: string;
      psFacebookEnsureTokensExchanged: string;
      psFacebookFbeOnboardingUninstallRoute: string;
      psFacebookUpdateCategoryMatch: string;
      psFacebookGetCategory: string;
      psFacebookGetCategories: string;
      psFacebookGetFeaturesRoute: string;
      psFacebookUpdateFeatureRoute: string;
      psFacebookGetCategoryMappingStatus: string;
      psFacebookRetrieveFaq: string;
      psFacebookUpdateConversionApiData: string;
      psFacebookGetProductsWithErrors: string;
      psFacebookGetProductSyncReporting: string;
      psFacebookGetProductStatuses: string;
      psFacebookRetrieveTokensRoute: string;
    }
  //   interface IntrinsicElements {
  //     [elem: string]: any;
  //   }
  // }
}
