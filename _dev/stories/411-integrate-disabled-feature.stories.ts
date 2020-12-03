import AvailableFeature from '../src/components/features/available-feature.vue';

/* global window */

export default {
  title: 'Integrate/2- Available Feature',
  component: AvailableFeature,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {AvailableFeature},
  template: '<available-feature :name="featureName" :active="properties.enabled"/>',
});

export const Default: any = Template.bind({});
Default.args = {
  featureName: 'messenger_chat',
  properties: {
    enabled: false,
  },
};
