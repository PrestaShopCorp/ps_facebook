import Integrate from '../src/views/integrate.vue';

/* global window */

export default {
  title: 'Integrate/Whole tab',
  component: Integrate,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {Integrate},
  template: '<integrate :enabledFeatures="enabledFeatures" :availableFeatures="disabledFeatures" :unavailableFeatures="unavailableFeatures" />',
});

export const NoFeaturesEnabled: any = Template.bind({});
NoFeaturesEnabled.args = {
  enabledFeatures: [],
  disabledFeatures: {
    "messenger_chat": {
      enabled: false
    },
    "page_cta": {
      enabled: false
    }
  },
  unavailableFeatures: {
    "ig_shopping": {
      enabled: false
    },
    "page_shop": {
      enabled: false
    }
  }
};
