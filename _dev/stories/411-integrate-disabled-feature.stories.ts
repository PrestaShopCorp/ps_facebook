import AvailableFeature from '../src/components/features/available-feature.vue';

/* global window */

export default {
  title: 'Integrate/2- Available Feature',
  component: AvailableFeature,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {AvailableFeature},
  template: '<available-feature :name="featureName" :active="properties.enabled" :manageRoute="manageRoute" />',
});

export const Default: any = Template.bind({});
Default.args = {
  featureName: 'messenger_chat',
  properties: {
    enabled: false,
  },
  manageRoute: {
    default: `https://www.facebook.com/facebook_business_extension?app_id=0&external_business_id=0`,
    page_cta: `https://www.facebook.com/0`,
  },
};
