import {AnalyticsBrowser} from '@segment/analytics-next';

declare module '*.vue' {
  import Vue from 'vue';

  export default Vue;
}

declare module 'vue/types/vue' {

  interface Vue {
    $segment: AnalyticsBrowser,
  }
}
