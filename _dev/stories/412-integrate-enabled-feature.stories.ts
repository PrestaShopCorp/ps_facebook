import EnabledFeature from '../src/components/features/enabled-feature.vue';

/* global window */

export default {
  title: 'Integrate/3- Active Feature',
  component: EnabledFeature,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {EnabledFeature},
  template: '<enabled-feature :name="featureName" :active="properties.enabled" :manageRoute="manageRoute" />',
});

// Enabled on FB side
export const EnabledOnFacebook: any = Template.bind({});
EnabledOnFacebook.args = {
  featureName: 'messenger_chat',
  properties: {
    enabled: true,
  },
  manageRoute: {
    default: `https://www.facebook.com/facebook_business_extension?app_id=0&external_business_id=0`,
    page_cta: `https://www.facebook.com/0`,
    view_message_url: `https://business.facebook.com/latest/inbox/all?asset_id=0`,
  },
};

// Disabled on FB side
export const DisabledOnFacebook: any = Template.bind({});
DisabledOnFacebook.args = {
  featureName: 'messenger_chat',
  properties: {
    enabled: false,
  },
  manageRoute: {
    default: `https://www.facebook.com/facebook_business_extension?app_id=0&external_business_id=0`,
    page_cta: `https://www.facebook.com/0`
  },
};
