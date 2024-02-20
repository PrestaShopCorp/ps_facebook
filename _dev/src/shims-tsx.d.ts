// import Vue, {VNode} from 'vue';
import {IContextAuthentication, IContextBase} from '@prestashopcorp/billing-cdc/dist/@types/context/ContextRoot';
import {ISubscription} from '@prestashopcorp/billing-cdc/dist/@types/Subscription';

declare global {
  // namespace JSX {
  //   // tslint:disable no-empty-interface
  //   interface Element extends VNode {}
  //   // tslint:disable no-empty-interface
  //   interface ElementClass extends Vue {}

    type ModuleCheck = {
      currentVersion: string,
      needsEnable: boolean,
      needsInstall: boolean,
      needsUpgrade: boolean,
      psFacebookEnableRoute: string,
      psFacebookInstallRoute: string,
      psFacebookUpgradeRoute: string,
      requiredVersion: string,
    };

    interface Window {
      contextPsAccounts: any;
      psAccountShopId: string|null;
      contextPsEventbus: any;
      psBillingContext?: IContextBase<IContextAuthentication>;
      psBillingSubscription?: ISubscription;
      psBilling: unknown;
      i18nSettings: any;

      psFacebookRouteToShopApi?: string;
      psFacebookProductsUrl?: string;
      psCloudSyncVersionCheck: ModuleCheck;

      psFacebookPixelActivationRoute: string;
      psFacebookFbeOnboardingSaveRoute: string;
      psFacebookEnsureTokensExchanged: string;
      psFacebookFbeOnboardingUninstallRoute: string;
      psFacebookUpdateCategoryMatch: string;
      psFacebookGetCategory: string;
      psFacebookGetCategories?: string;
      psFacebookGetFeaturesRoute: string;
      psFacebookUpdateFeatureRoute: string;
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
