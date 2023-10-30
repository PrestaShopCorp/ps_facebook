/**
 * 2007-2021 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import { addDecorator } from '@storybook/vue';
import Vue from 'vue';
import { select } from '@storybook/addon-knobs'

// import vue plugins
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue';
import VueI18n from 'vue-i18n';
import VueSegment from '@/lib/segment';

// import css style
import 'bootstrap-vue/dist/bootstrap-vue';
import 'prestakit/dist/css/bootstrap-prestashop-ui-kit.css';
Vue.use(BootstrapVue, BootstrapVueIcons);

import {messages, locales} from '@/lib/translations';
import store from '@/store';

// import css style
// theme.css v1.7.5 from the Back-Office
// all font import are commented to avoid 404
import '!style-loader!css-loader?url=false!./assets/theme.css';
// shame.css is a set of rules to better mimic the BO behavior in a shameful way
import '!style-loader!css-loader?url=false!./assets/shame.css';
// app.scss all the styles for the module
import '!style-loader!css-loader!sass-loader!../src/assets/scss/app.scss';

// Mock to simulate a FB onboarding popup
window.psFacebookGenerateOpenPopup = (component) => () => {
  component.onFbeOnboardOpened();
  console.log('Simulating FB popup...');
  setTimeout(() => {
    component.onFbeOnboardResponded({
      access_token: 'azeazeaze'
    }, () => Promise.resolve());
  }, 3000);
};

Vue.config.ignoredElements = ['prestashop-accounts'];
// i18n and store
Vue.use(VueI18n);
Vue.use(VueSegment, {
  id: 'dummyID',
  debug: true,
  pageCategory: '[GGL]',
});
addDecorator(() => ({
  template: `
    <div
      class='nobootstrap'
      style='
        background: none;
        padding: 0;
        min-width: 0;
    '>
      <div id='psFacebookApp'>
        <div class='ps_gs-sticky-head'>
          <b-toaster
            name='b-toaster-top-right'
            class='ps_gs-toaster-top-right'
          />
        </div>
        <story />
      </div>
    </div>
    `,
    i18n: new VueI18n({
    locale: 'en',
    locales: locales,
    messages: messages,
  }),
  props: {
    storybookLocale: {
      type: String,
      default: select('I18n locale', locales, 'en'),
    },
  },
  watch: {
    // add a watcher to toggle language
    storybookLocale: {
      handler() {
        this.$i18n.locale = this.storybookLocale;
      },
      immediate: true,
    },
  },
  beforeCreate() {
    window.i18nSettings = {
      languageLocale: 'en-us',
      isoCode: 'en',
    }
  },
  store,
}));

export const parameters = {
  actions: { argTypesRegex: "^on[A-Z].*" },
  backgrounds: {
    default: 'backOffice',
    values: [
      {
        name: 'backOffice',
        value: '#F1F1F1'
      },
      {
        name: 'white',
        value: '#ffffff'
      },
      {
        name: 'black',
        value: '#000000'
      },
    ],
  }
}
