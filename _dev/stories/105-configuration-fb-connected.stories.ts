import FacebookConnected from '../src/components/configuration/facebook-connected.vue';
// @ts-ignore
import facebookLogo from '../src/assets/facebook_logo.svg';

const contextPsFacebook = {
  user: {
    email: 'him@prestashop.com',
  },
  facebookBusinessManager: {
    name: 'La Fanchonette',
    email: 'fanchonette@ps.com',
    createdAt: '2024-01-10T14:29:09+0100',
    id: '12345689',
  },
  pixel: {
    name: 'La Fanchonette Test Pixel',
    id: '1234567890',
    lastActive: '2024-01-10T14:29:09+0100',
    isActive: true,
  },
  page: {
    page: 'La Fanchonette',
    likes: 42,
    logo: facebookLogo,
  },
  ads: {
    name: 'La Fanchonette',
    createdAt: '2024-01-10T14:29:09+0100',
  },
};

const contextPsFacebookOverflows = {
  user: {
    email: 'a.very.long.email.should.not.be.a.problem.even.if.this.email.cannot.be.so.long@prestashop.com',
  },
  facebookBusinessManager: {
    name: 'La Fanchonette qui s\'étend sur la longueur',
    email: 'fanchonette.a.very.long.email.should.not.be.a.problem.even.if.its.too.long@ps.com',
    createdAt: '2024-01-10T14:29:09+0100',
  },
  pixel: {
    name: 'La Fanchonette qui s\'étend sur la longueur Test Pixel',
    id: '123456789012345678901234567890',
    lastActive: '2024-01-10T14:29:09+0100',
    isActive: true,
  },
  page: {
    page: 'La Fanchonette qui s\'étend sur la longueur',
    likes: 42,
    logo: facebookLogo,
  },
  ads: {
    name: 'La Fanchonette qui s\'étend sur la longueur',
    createdAt: '2024-01-10T14:29:09+0100',
  },
};

const contextPsFacebookMissingPermissions = {
  user: {
    email: 'him@prestashop.com',
  },
  facebookBusinessManager: {
    name: 'La Fanchonette',
    email: 'fanchonette@ps.com',
    createdAt: '2024-01-10T14:29:09+0100',
    id: '12345689',
  },
  pixel: {
    name: 'La Fanchonette Test Pixel',
    id: '1234567890',
    lastActive: '2024-01-10T14:29:09+0100',
    isActive: true,
  },
  page: {
    page: null,
    likes: null,
    logo: null,
  },
  ads: {
    name: 'La Fanchonette',
    createdAt: '2024-01-10T14:29:09+0100',
  },
};

export default {
  title: 'Configuration/Facebook connected',
  component: FacebookConnected,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {FacebookConnected},
  template: '<facebook-connected :contextPsFacebook="contextPsFacebook" :psFacebookAppId="psFacebookAppId" :externalBusinessId="externalBusinessId" @onEditClick="onEditClick" @onPixelActivation="onPixelActivation" />',
});
export const Default:any = Template.bind({});
Default.args = {
  psFacebookAppId: '1234567890',
  externalBusinessId: '0987654321',
  contextPsFacebook,
};
export const OverflowData:any = Template.bind({});
OverflowData.args = {
  psFacebookAppId: '1234567890',
  externalBusinessId: '0987654321',
  contextPsFacebook: contextPsFacebookOverflows,
};

export const MissingPermissions:any = Template.bind({});
MissingPermissions.args = {
  psFacebookAppId: '1234567890',
  externalBusinessId: '0987654321',
  contextPsFacebook: contextPsFacebookMissingPermissions,
};
