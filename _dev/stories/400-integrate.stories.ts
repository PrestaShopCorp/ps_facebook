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

export const MessengerEnabled: any = Template.bind({});
MessengerEnabled.args = {
  enabledFeatures: {
    "messenger_chat": {
      enabled: true
    },
  },
  disabledFeatures: {
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

export const WithCatalogSynchronized: any = Template.bind({});
WithCatalogSynchronized.args = {
  enabledFeatures: {
    "messenger_chat": {
      enabled: true
    },
  },
  disabledFeatures: {
    "page_cta": {
      enabled: false
    },
    "ig_shopping": {
      enabled: false
    },
    "page_shop": {
      enabled: false
    }
  },
  unavailableFeatures: {
  }
};

export const AllFeaturesEnabled: any = Template.bind({});
AllFeaturesEnabled.args = {
  enabledFeatures: {
    "messenger_chat": {
      enabled: true
    },
    "page_cta": {
      enabled: true
    },
    "ig_shopping": {
      enabled: true
    },
    "page_shop": {
      enabled: false
    },
  },
  disabledFeatures: {
  },
  unavailableFeatures: {
  }
};

export const NoFeature: any = Template.bind({});
NoFeature.args = {
  enabledFeatures: {
  },
  disabledFeatures: {
  },
  unavailableFeatures: {
  }
};
