import { configure, addDecorator } from '@storybook/vue';
import Vue from 'vue';

// import vue plugins
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue';
import VueI18n from 'vue-i18n';

// import css style
import 'bootstrap-vue/dist/bootstrap-vue';
import 'prestakit/dist/css/bootstrap-prestashop-ui-kit.css';
Vue.use(BootstrapVue, BootstrapVueIcons);

// i18n
Vue.use(VueI18n);
addDecorator(() => ({
  template: '<story/>',
  i18n: new VueI18n({
    locale: 'en',
    messages: require('./translations.json'),
  })
}));

configure(require.context('../src', true, /\.stories\.js$/), module);

