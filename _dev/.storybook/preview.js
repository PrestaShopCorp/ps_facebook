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
import Vue, { defineComponent } from 'vue';

// import vue plugins
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue';
import VueI18n from 'vue-i18n';
import {useGlobals} from '@storybook/client-api';
import VueSegment from '@/lib/segment';

// import css style
import 'bootstrap-vue/dist/bootstrap-vue';
import 'prestakit/dist/css/bootstrap-prestashop-ui-kit.css';
Vue.use(BootstrapVue, BootstrapVueIcons);

import i18n, {availableLocales, loadLanguageAsync} from '@/lib/i18n.ts';
import store from '@/store';

// app.scss all the styles for the module
import '../src/assets/scss/app.scss';

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
export const decorators = [
  (story, context) => {
    const [{storybookLocale}] = useGlobals();
    loadLanguageAsync(storybookLocale);

    return defineComponent({
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
      i18n,
      data() {
        return {
          storybookLocale,
        }
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
    });
  },
];

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

export const globalTypes = {
  storybookLocale: {
    description: 'Internationalization locale',
    defaultValue: 'en',
    toolbar: {
      icon: 'globe',
      items: availableLocales.map((languageLocale) => ({
        value: languageLocale,
        title: new Intl.DisplayNames(
            [navigator.language || 'en'],
            {type: 'language'},
          ).of(languageLocale),
        right: String.fromCodePoint(...(languageLocale === 'en' ? 'gb': languageLocale)
          .toUpperCase()
          .split('')
          .map(char =>  127397 + char.charCodeAt())),
      })),
    },
  },
};
