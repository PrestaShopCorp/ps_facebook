<template>
  <b-card
    v-if="showSurvey"
    no-body
    class="m-3 mt-4 p-3 text-center survey"
  >
    <h3><i class="material-icons">mood</i>  {{ $t('survey.title') }}</h3>
    <p class="small-text">
      {{ $t('survey.text') }}
    </p>
    <b-button
      size="sm"
      variant="secondary"
      @click="onSurvey()"
      href="https://forms-prestashop.typeform.com/to/X9FKIl3e"
      target="_blank"
    >
      {{ $t('survey.button') }}
    </b-button>
  </b-card>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BCard, BButton} from 'bootstrap-vue';

export default defineComponent({
  name: 'Survey',
  components: {
    BCard,
    BButton,
  },
  props: {
    locale: {
      type: String,
      required: false,
      default: () => global.psFacebookLocale || 'en-US',
    },
  },
  methods: {
    onSurvey() {
      this.$segment.track('Take the survey', {
        module: 'ps_facebook',
      });
    },
  },
  computed: {
    showSurvey() {
      return ['en', 'fr'].includes(this.locale.substring(0, 2));
    },
  },
});
</script>

<style lang="scss" scoped>
  .survey {
    display: block !important;
    background-color: rgba(255,255,255,0.3) !important;
    border: 1px solid #dde3e6 !important;
    border-radius: 3px;
  }
</style>
