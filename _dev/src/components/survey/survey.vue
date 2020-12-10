<template>
  <b-card
    no-body
    class="m-3 mt-4 p-3 text-center survey"
  >
    <h3><i class="material-icons">mood</i>  {{ $t('survey.title') }}</h3>
    <p class="small-text">{{ $t('survey.text') }}</p>
    <b-button
      size="sm"
      variant="secondary"
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
