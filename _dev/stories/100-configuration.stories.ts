import Configuration from '../src/views/configuration.vue';

/* global window */

export default {
  title: 'Configuration/Whole tab',
  component: Configuration,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {Configuration},
  template: '<configuration :contextPsAccounts="contextPsAccounts" :contextPsFacebook="contextPsFacebook" />',
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
};

export const HalfConnected: any = Template.bind({});
HalfConnected.args = {
  contextPsAccounts: window.contextPsAccounts,
  contextPsFacebook: null,
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
      activated: true,
    },
    page: {
      name: 'La Fanchonette',
      likes: 42,
      logo: null,
    },
    ads: {
      name: 'La Fanchonette',
      email: 'fanchonette@ps.com',
      createdAt: Date.now(),
    },
  },
};
