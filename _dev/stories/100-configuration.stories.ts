import Configuration from '../src/views/configuration.vue';

/* global window */

export default {
  title: 'Configuration/Whole tab',
  component: Configuration,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {Configuration},
  template: '<configuration :contextPsAccounts="contextPsAccounts" :contextPsFacebook="contextPsFacebook" :externalBusinessId="externalBusinessId" :psAccountsToken="psAccountsToken" :currency="currency" :timezone="timezone" :locale="locale" :pixelActivationRoute="pixelActivationRoute" :fbeOnboardingSaveRoute="fbeOnboardingSaveRoute" :psFacebookUiUrl="psFacebookUiUrl" :psFacebookRetrieveExternalBusinessId="psFacebookRetrieveExternalBusinessId" />',
});

export const NoPsAccountOnboarded: any = Template.bind({});
NoPsAccountOnboarded.args = {
  contextPsAccounts: {
    psIs17: true,
    currentShop: {
      id: 1,
      name: 'PrestaShop',
      url: 'http://my-domain.com/admin-dev/blabla',
      domain: 'my-domain.com',
      domainSsl: 'my-secure-domain.com',
    },
    shops: [
      {
        id: 1,
        name: 'Default',
        shops: [
          {
            id: 1,
            name: 'PrestaShop',
            url: 'http://my-domain.com/admin-dev/blabla',
            domain: 'my-domain.com',
            domainSsl: 'my-secure-domain.com',
          },
        ],
      },
    ],
    user: {email: null, emailIsValidated: false, isSuperAdmin: true},
    onboardingLink: 'https://perdu.com',
    superAdminEmail: 'nobody@prestashop.com',
    ssoResendVerificationEmail: null,
    manageAccountLink: 'https://perdu.com',
  },
  contextPsFacebook: null,
  externalBusinessId: '0b2f5f57-5190-47e2-8df6-b2f96447ac9f',
  psAccountsToken: null,
  currency: 'EUR',
  timezone: 'Europe/Paris',
  locale: 'fr-FR',
  pixelActivationRoute: 'http://perdu.com',
  fbeOnboardingSaveRoute: 'http://perdu.com',
  psFacebookUiUrl: 'https://facebook.psessentials.net/index.html',
  psFacebookRetrieveExternalBusinessId: 'http://perdu.com',
};

export const HalfConnected: any = Template.bind({});
HalfConnected.args = {
  contextPsAccounts: window.contextPsAccounts,
  contextPsFacebook: {},
  externalBusinessId: '0b2f5f57-5190-47e2-8df6-b2f96447ac9f',
  psAccountsToken: 'a-valid-token',
  currency: 'EUR',
  timezone: 'Europe/Paris',
  locale: 'fr-FR',
  pixelActivationRoute: 'http://perdu.com',
  fbeOnboardingSaveRoute: 'http://perdu.com',
  psFacebookUiUrl: 'https://facebook.psessentials.net/index.html',
  psFacebookRetrieveExternalBusinessId: 'http://perdu.com',
};

export const FullConnected: any = Template.bind({});
FullConnected.args = {
  contextPsAccounts: window.contextPsAccounts,
  contextPsFacebook: {
    email: 'him@prestashop.com',
    facebookBusinessManager: {
      name: 'La Fanchonette',
      email: 'fanchonette@ps.com',
      createdAt: Date.now(),
    },
    pixel: {
      name: 'La Fanchonette Test Pixel',
      id: '1234567890',
      lastActive: Date.now(),
      isActive: true,
    },
    page: {
      page: 'La Fanchonette',
      likes: 42,
      logo: null,
    },
    ads: {
      name: 'La Fanchonette',
      email: 'fanchonette@ps.com',
      createdAt: Date.now(),
    },
    categoriesMatching: {
      sent: false,
    },
  },
  externalBusinessId: '0b2f5f57-5190-47e2-8df6-b2f96447ac9f',
  psAccountsToken: 'a-valid-token',
  currency: 'EUR',
  timezone: 'Europe/Paris',
  locale: 'fr-FR',
  pixelActivationRoute: 'http://perdu.com',
  fbeOnboardingSaveRoute: 'http://perdu.com',
  psFacebookUiUrl: 'https://facebook.psessentials.net/index.html',
  psFacebookRetrieveExternalBusinessId: 'http://perdu.com',
};
