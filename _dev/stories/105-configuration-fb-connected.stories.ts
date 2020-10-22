import FacebookConnected from '../src/components/configuration/facebook-connected.vue';
// @ts-ignore
import facebookLogo from '../src/assets/facebook_logo.svg';

const contextPsFacebook = {
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
    name: 'La Fanchonette',
    likes: 42,
    logo: facebookLogo,
  },
  ads: {
    name: 'La Fanchonette',
    email: 'fanchonette@ps.com',
    createdAt: Date.now(),
  },
};

const contextPsFacebookOverflows = {
  email: 'a.very.long.email.should.not.be.a.problem.even.if.this.email.cannot.be.so.long@prestashop.com',
  facebookBusinessManager: {
    name: 'La Fanchonette qui s\'étend sur la longueur',
    email: 'fanchonette.a.very.long.email.should.not.be.a.problem.even.if.its.too.long@ps.com',
    createdAt: Date.now(),
  },
  pixel: {
    name: 'La Fanchonette qui s\'étend sur la longueur Test Pixel',
    id: '123456789012345678901234567890',
    lastActive: Date.now(),
    isActive: true,
  },
  page: {
    name: 'La Fanchonette qui s\'étend sur la longueur',
    likes: 42,
    logo: facebookLogo,
  },
  ads: {
    name: 'La Fanchonette qui s\'étend sur la longueur',
    email: 'fanchonette.a.very.long.email.should.not.be.a.problem.even.if.its.too.long@ps.com',
    createdAt: Date.now(),
  },
};

export default {
  title: 'Configuration/Facebook connected',
  component: FacebookConnected,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {FacebookConnected},
  template: '<facebook-connected :contextPsFacebook="contextPsFacebook" :startExpanded="startExpanded" @onEditClick="onEditClick" @onPixelActivation="onPixelActivation" />',
});
export const Default:any = Template.bind({});
Default.args = {
  contextPsFacebook,
};
export const DefaultFolded:any = Template.bind({});
DefaultFolded.args = {
  contextPsFacebook,
  startExpanded: false,
};
export const OverflowData:any = Template.bind({});
OverflowData.args = {
  contextPsFacebook: contextPsFacebookOverflows,
};
