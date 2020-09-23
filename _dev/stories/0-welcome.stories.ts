import { linkTo } from '@storybook/addon-links';
import Welcome from './welcome.vue';

export default {
  title: 'Intro',
  component: Welcome,
};

export const WelcomeToStorybook = () => ({
  components: { Welcome },
  template: '<welcome :showApp="action" />',
  methods: { action: linkTo('Button') },
});
WelcomeToStorybook.story = {
  name: 'Welcome to Storybook',
};
