<template>
  <b-card
    bg-variant="info"
    no-body
    class="ps_sc_featurescard"
  >
    <b-card-body
      class="d-flex mx-5 my-4"
    >
      <div>
        <img
          class="mx-4 d-none d-md-block"
          src="@/assets/prestashop-social-1-1.png"
          width="369"
          alt="PrestaShop Social illustration"
        >
      </div>
      <div>
        <p class="ps_sc_featurescard__title">
          {{ $t('configuration.keyFeatures.catchPhrase') }}
        </p>

        <div
          class="ps_sc_featurescard__list font-weight-600 ps_gs-fz-18"
          v-html="md2html($t('configuration.keyFeatures.features'))"
        />
      </div>
    </b-card-body>
  </b-card>
</template>

<script lang="ts">
import Showdown from 'showdown';
import {defineComponent} from 'vue';
import {TranslateResult} from 'vue-i18n';

export default defineComponent({
  name: 'KeyFeatures',
  methods: {
    md2html: (md: string|TranslateResult) => (new Showdown.Converter()).makeHtml(md),
  },
});
</script>
