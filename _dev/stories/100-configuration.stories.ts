import Configuration from "../src/views/configuration.vue";
import {contextPsAccountsNotConnected, contextPsAccountsConnected, contextPsAccountsConnectedAndValidated} from "../.storybook/mock/ps-accounts";

export default {
  title: "Configuration/Whole tab",
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
  ':psFacebookRetrieveExternalBusinessId="psFacebookRetrieveExternalBusinessId" ' +
  ':psFacebookAppId="psFacebookAppId"' +
  ':psAccountsVersionCheck="psAccountsVersionCheck"' +
  ':psCloudSyncVersionCheck="psCloudSyncVersionCheck"';

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { Configuration },
  template: `<configuration ${params} />`,
  beforeMount: args.beforeMount,
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
  psFacebookRetrieveExternalBusinessId: "http://perdu.com",
  psAccountsVersionCheck,
  psCloudSyncVersionCheck,
};

export const HalfConnected: any = Template.bind({});
HalfConnected.args = {
  beforeMount: function(this: any) {
    window.contextPsAccounts = Object.assign({}, contextPsAccountsConnectedAndValidated);
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
  psFacebookRetrieveExternalBusinessId: "http://perdu.com",
  psAccountsVersionCheck,
  psCloudSyncVersionCheck,
};

export const FullConnected: any = Template.bind({});
FullConnected.args = {
  beforeMount: function(this: any) {
    window.contextPsAccounts = Object.assign({}, contextPsAccountsConnectedAndValidated);
  },
  contextPsAccounts: contextPsAccountsConnectedAndValidated,
  contextPsFacebook: {
    user: {
      email: "him@prestashop.com",
    },
    facebookBusinessManager: {
      name: "La Fanchonette",
      email: "fanchonette@ps.com",
      createdAt: Date.now(),
      id: "12345689",
    },
    pixel: {
      name: "La Fanchonette Test Pixel",
      id: "1234567890",
      lastActive: Date.now(),
      isActive: true,
    },
    page: {
      page: "La Fanchonette",
      likes: 42,
      logo: null,
    },
    ads: {
      name: "La Fanchonette",
      email: "fanchonette@ps.com",
      createdAt: Date.now(),
    },
    catalog: {
      categoryMatchingStarted: false,
      productSyncStarted: false,
    },
  },
  psFacebookAppId: "1234567890",
  externalBusinessId: "0b2f5f57-5190-47e2-8df6-b2f96447ac9f",
  psAccountsToken: "a-valid-token",
  currency: "EUR",
  timezone: "Europe/Paris",
  locale: "fr-FR",
  pixelActivationRoute: "http://perdu.com",
  fbeOnboardingSaveRoute: "http://perdu.com",
  psFacebookUiUrl: "https://facebook.psessentials.net/index.html",
  psFacebookRetrieveExternalBusinessId: "http://perdu.com",
  psAccountsVersionCheck,
  psCloudSyncVersionCheck,
};

