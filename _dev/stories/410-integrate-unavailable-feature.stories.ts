import UnavailableFeature from '../src/components/features/unavailable-feature.vue';

/* global window */

export default {
  title: 'Integrate/1- Unavailable Feature',
  component: UnavailableFeature,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {UnavailableFeature},
  template: '<unavailable-feature :name="featureName" />',
});

export const Default: any = Template.bind({});
Default.args = {
  featureName: 'messenger_chat',
};
