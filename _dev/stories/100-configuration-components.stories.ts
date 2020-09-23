import VueI18n from 'vue-i18n';
import { storiesOf } from '@storybook/vue'
import { action } from '@storybook/addon-actions'
import { linkTo } from '@storybook/addon-links'

import Introduction from '../src/components/configuration/introduction.vue';
import NoConfig from '../src/components/configuration/no-config.vue';
import Messages from '../src/components/configuration/messages.vue';

storiesOf('Configuration components', module)

.add('Introduction', (() => ({
  components: { Introduction },
  template: '<introduction />',
})))

.add('No config block', (() => ({
  components: { NoConfig },
  template: '<no-config />',
})))

.add('Top messages', (() => ({
  components: { Messages },
  template: '<messages :showOnboardSucceeded="true" :showSyncCatalogAdvice="true" />',
})))
