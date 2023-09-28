import Vue, {VNode} from 'vue';

declare global {
  // namespace JSX {
  //   // tslint:disable no-empty-interface
  //   interface Element extends VNode {}
  //   // tslint:disable no-empty-interface
  //   interface ElementClass extends Vue {}

    interface Window {
      contextPsAccounts: any;
      contextPsEventbus: any;
      psBillingContext: any;
      i18nSettings: any;

      psFacebookRouteToShopApi?: string;

      psFacebookPixelActivationRoute: string;
      psFacebookFbeOnboardingSaveRoute: string;
      psFacebookEnsureTokensExchanged: string;
      psFacebookFbeOnboardingUninstallRoute: string;
      psFacebookUpdateCategoryMatch: string;
      psFacebookGetCategory: string;
      psFacebookGetCategories: string;
      psFacebookGetFeaturesRoute: string;
      psFacebookUpdateFeatureRoute: string;
      psFacebookStartProductSyncRoute: string;
      psFacebookGetCatalogSummaryRoute: string;
      psFacebookRunPrevalidationScanRoute: string;
      psFacebookGetCategoryMappingStatus: string;
      psFacebookRetrieveFaq: string;
      psFacebookUpdateConversionApiData: string;
      psFacebookGetProductsWithErrors: string;
      psFacebookGetProductSyncReporting: string;
      psFacebookGetProductStatuses: string;
      psFacebookExportWholeCatalog: string;
      psFacebookRetrieveTokensRoute: string;
    }
  //   interface IntrinsicElements {
  //     [elem: string]: any;
  //   }
  // }
}
