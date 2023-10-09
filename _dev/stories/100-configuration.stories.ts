import cloneDeep from 'lodash.clonedeep';
import Configuration from "@/views/configuration.vue";
import {contextPsAccountsNotConnected, contextPsAccountsConnectedAndValidated} from "@/../.storybook/mock/ps-accounts";
import {contextPsEventBus} from "@/../.storybook/mock/ps-event-bus";
import {contextPsBilling, runningSubscription} from "@/../.storybook/mock/ps-billing";
import {contextFacebookOnboarded} from "@/../.storybook/mock/onboarding";
import {State as CatalogState} from '../src/store/modules/catalog/state';

export default {
  title: "Configuration/Configuration page",
  component: Configuration,
};

const psAccountsVersionCheck = {
  needsInstall: false,
  needsEnable: false,
  needsUpgrade: false,
  currentVersion: "5.3.0",
  requiredVersion: "4.0.0",
  psFacebookUpgradeRoute: "#",
  psFacebookInstallRoute: "#",
  psFacebookEnableRoute: "#",
};
const psCloudSyncVersionCheck = {
  needsInstall: false,
  needsEnable: false,
  needsUpgrade: false,
  currentVersion: "1.9.9",
  requiredVersion: "1.9.4",
  psFacebookUpgradeRoute: "#",
  psFacebookInstallRoute: "#",
  psFacebookEnableRoute: "#",
};


const params =
  ':contextPsAccounts="contextPsAccounts" :contextPsFacebook="contextPsFacebook" ' +
  ':externalBusinessId="externalBusinessId" :psAccountsToken="psAccountsToken" ' +
  ':currency="currency" :timezone="timezone" :locale="locale" ' +
  ':pixelActivationRoute="pixelActivationRoute" :fbeOnboardingSaveRoute="fbeOnboardingSaveRoute" ' +
  ':psFacebookUiUrl="psFacebookUiUrl" ' +
  ':psFacebookAppId="psFacebookAppId"' +
  ':psAccountsVersionCheck="psAccountsVersionCheck"' +
  ':psCloudSyncVersionCheck="psCloudSyncVersionCheck"';

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { Configuration },
  template: `<configuration ${params} ref="page" />`,
  beforeMount: args.beforeMount,
  mounted: args.mounted,
});

export const NoPsAccountOnboarded: any = Template.bind({});
NoPsAccountOnboarded.args = {
  beforeMount: function(this: any) {
    window.contextPsAccounts = Object.assign({}, contextPsAccountsNotConnected);
  },
  contextPsAccounts: contextPsAccountsNotConnected,
  contextPsFacebook: null,
  psFacebookAppId: "1234567890",
  externalBusinessId: null,
  psAccountsToken: null,
  currency: "EUR",
  timezone: "Europe/Paris",
  locale: "fr-FR",
  pixelActivationRoute: "http://perdu.com",
  fbeOnboardingSaveRoute: "http://perdu.com",
  psFacebookUiUrl: "https://facebook.psessentials.net/index.html",
  psAccountsVersionCheck,
  psCloudSyncVersionCheck,
};

export const NoActiveSubscription: any = Template.bind({});
NoActiveSubscription.args = {
  beforeMount: function(this: any) {
    window.contextPsAccounts = Object.assign({}, contextPsAccountsConnectedAndValidated);
    window.psBillingContext = cloneDeep(contextPsBilling);
    this.$store.state.app.billing.subscription = undefined;
    window.contextPsEventbus = cloneDeep(contextPsEventBus);
  },
  contextPsAccounts: contextPsAccountsConnectedAndValidated,
  contextPsFacebook: {},
  psFacebookAppId: "1234567890",
  externalBusinessId: "0b2f5f57-5190-47e2-8df6-b2f96447ac9f",
  psAccountsToken: "a-valid-token",
  currency: "EUR",
  timezone: "Europe/Paris",
  locale: "fr-FR",
  pixelActivationRoute: "http://perdu.com",
  fbeOnboardingSaveRoute: "http://perdu.com",
  psFacebookUiUrl: "https://facebook.psessentials.net/index.html",
  psAccountsVersionCheck,
  psCloudSyncVersionCheck,
};

export const NotConnectedOnFb: any = Template.bind({});
NotConnectedOnFb.args = {
  beforeMount: function(this: any) {
    window.contextPsAccounts = Object.assign({}, contextPsAccountsConnectedAndValidated);
    window.psBillingContext = cloneDeep(contextPsBilling);
    this.$store.state.app.billing.subscription = runningSubscription;
    window.contextPsEventbus = cloneDeep(contextPsEventBus);
  },
  mounted: function (this: any) {
    this.$refs.page.$data.billingRunning = true;
  },
  contextPsAccounts: contextPsAccountsConnectedAndValidated,
  contextPsFacebook: {},
  psFacebookAppId: "1234567890",
  externalBusinessId: "0b2f5f57-5190-47e2-8df6-b2f96447ac9f",
  psAccountsToken: "a-valid-token",
  currency: "EUR",
  timezone: "Europe/Paris",
  locale: "fr-FR",
  pixelActivationRoute: "http://perdu.com",
  fbeOnboardingSaveRoute: "http://perdu.com",
  psFacebookUiUrl: "https://facebook.psessentials.net/index.html",
  psAccountsVersionCheck,
  psCloudSyncVersionCheck,
};

export const FullyConnected: any = Template.bind({});
FullyConnected.args = {
  beforeMount: function(this: any) {
    window.contextPsAccounts = Object.assign({}, contextPsAccountsConnectedAndValidated);
    window.psBillingContext = cloneDeep(contextPsBilling);
    this.$store.state.app.billing.subscription = runningSubscription;
    window.contextPsEventbus = cloneDeep(contextPsEventBus);
    (this.$store.state.catalog as CatalogState).enabledFeature = false;
  },
  mounted: function (this: any) {
    this.$refs.page.$data.billingRunning = true;
    this.$refs.page.$data.psFacebookJustOnboarded = true;
  },
  contextPsAccounts: contextPsAccountsConnectedAndValidated,
  contextPsFacebook: contextFacebookOnboarded,
  psFacebookAppId: "1234567890",
  externalBusinessId: "0b2f5f57-5190-47e2-8df6-b2f96447ac9f",
  psAccountsToken: "a-valid-token",
  currency: "EUR",
  timezone: "Europe/Paris",
  locale: "fr-FR",
  pixelActivationRoute: "http://perdu.com",
  fbeOnboardingSaveRoute: "http://perdu.com",
  psFacebookUiUrl: "https://facebook.psessentials.net/index.html",
  psAccountsVersionCheck,
  psCloudSyncVersionCheck,
};

export const FullyConnectedAndSyncing: any = Template.bind({});
FullyConnectedAndSyncing.args = {
  beforeMount: function(this: any) {
    window.contextPsAccounts = Object.assign({}, contextPsAccountsConnectedAndValidated);
    window.psBillingContext = cloneDeep(contextPsBilling);
    this.$store.state.app.billing.subscription = runningSubscription;
    window.contextPsEventbus = cloneDeep(contextPsEventBus);
    (this.$store.state.catalog as CatalogState).enabledFeature = true;
  },
  mounted: function (this: any) {
    this.$refs.page.$data.billingRunning = true;
  },
  contextPsAccounts: contextPsAccountsConnectedAndValidated,
  contextPsFacebook: contextFacebookOnboarded,
  psFacebookAppId: "1234567890",
  externalBusinessId: "0b2f5f57-5190-47e2-8df6-b2f96447ac9f",
  psAccountsToken: "a-valid-token",
  currency: "EUR",
  timezone: "Europe/Paris",
  locale: "fr-FR",
  pixelActivationRoute: "http://perdu.com",
  fbeOnboardingSaveRoute: "http://perdu.com",
  psFacebookUiUrl: "https://facebook.psessentials.net/index.html",
  psAccountsVersionCheck,
  psCloudSyncVersionCheck,
};

