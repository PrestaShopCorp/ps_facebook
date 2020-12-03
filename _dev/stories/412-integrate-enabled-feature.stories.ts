import EnabledFeature from '../src/components/features/enabled-feature.vue';

/* global window */

export default {
  title: 'Integrate/3- Active Feature',
  component: EnabledFeature,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {EnabledFeature},
  template: '<enabled-feature :name="featureName" :active="properties.enabled"/>',
});

// Enabled on FB side
export const EnabledOnFacebook: any = Template.bind({});
EnabledOnFacebook.args = {
  featureName: 'messenger_chat',
  properties: {
    enabled: true,
  },
};

// Disabled on FB side
export const DisabledOnFacebook: any = Template.bind({});
DisabledOnFacebook.args = {
  featureName: 'messenger_chat',
  properties: {
    enabled: false,
  },
};
