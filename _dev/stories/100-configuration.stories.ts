import Configuration from '../src/views/configuration.vue';

export default {
  title: 'Configuration/Whole tab',
  component: Configuration,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { Configuration },
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
          }
        ],
      }
    ],
    user: { email: null, emailIsValidated: false, isSuperAdmin: true },
    onboardingLink: 'https://perdu.com',
    superAdminEmail: 'nobody@prestashop.com',
    ssoResendVerificationEmail: null,
    manageAccountLink: 'https://perdu.com',
  },
  contextPsFacebook: null
};

export const HalfConnected: any = Template.bind({});
HalfConnected.args = {
  contextPsAccounts: global.contextPsAccounts,
  contextPsFacebook: null
};

export const FullConnected: any = Template.bind({});
FullConnected.args = {
  contextPsAccounts: global.contextPsAccounts,
  contextPsFacebook: {
    // TODO !0: formaliser
  }
};

