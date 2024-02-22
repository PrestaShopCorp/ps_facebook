<template>
  <b-card class="m-3">
    <div class="p-3">
      <div>
        <img
          class="mb-3"
          src="@/assets/logo-wordmark.svg"
          width="420"
          alt="PrestaShop Social logo"
        >
      </div>
      <div class="font-weight-600 mb-5">
        {{ $t('configuration.introduction.subTitle') }}
      </div>

      <b-button
        class="mb-2 mt-1"
        variant="primary"
        @click="goToConfiguration"
      >
        {{ $t('configuration.introduction.getStarted') }}
      </b-button>
      <hr>

      <div class="d-flex flex-column flex-md-row align-items-center mb-2">
        <img
          class="mx-5 d-none d-md-block"
          src="@/assets/PS-SOCIAL-synchronize.svg"
          width="150"
          height="150"
          alt="A potatoe"
        >
        <div class="mb-4 mb-md-0">
          <h1
            class="mb-1"
          >
            {{ $t('configuration.introduction.proPoint1Title') }}
          </h1>
          <p>{{ $t('configuration.introduction.proPoint1Description') }}</p>
        </div>
      </div>

      <div class="d-flex flex-column flex-md-row align-items-center mb-2">
        <img
          class="mx-5 d-none d-md-block"
          src="@/assets/PS-SOCIAL-smartads.svg"
          width="150"
          height="150"
          alt="A potatoe"
        >
        <div class="mb-4 mb-md-0">
          <h1
            class="mb-1"
          >
            {{ $t('configuration.introduction.proPoint2Title') }}
          </h1>
          <p>{{ $t('configuration.introduction.proPoint2Description') }}</p>
        </div>
      </div>

      <div class="d-flex flex-column flex-md-row align-items-center mb-2">
        <img
          class="mx-5 d-none d-md-block"
          src="@/assets/PS-SOCIAL-data.svg"
          width="150"
          height="150"
          alt="A potatoe"
        >
        <div class="mb-4 mb-md-0">
          <h1
            class="mb-1"
          >
            {{ $t('configuration.introduction.proPoint3Title') }}
          </h1>
          <p>{{ $t('configuration.introduction.proPoint3Description') }}</p>
        </div>
      </div>

      <div class="d-flex flex-column flex-md-row align-items-center mb-2">
        <img
          class="mx-5 d-none d-md-block"
          src="@/assets/PS-SOCIAL-messenger.svg"
          width="150"
          height="150"
          alt="A potatoe"
        >
        <div class="mb-4 mb-md-0">
          <h1
            class="mb-1"
          >
            {{ $t('configuration.introduction.proPoint4Title') }}
          </h1>
          <p>{{ $t('configuration.introduction.proPoint4Description') }}</p>
        </div>
      </div>

      <hr>
      <div class="text-center font-weight-600">
        <p>{{ $t('configuration.introduction.resume') }}</p>
        <b-button
          variant="primary"
          @click="goToConfiguration"
        >
          {{ $t('configuration.introduction.getStarted') }}
        </b-button>
      </div>
    </div>
  </b-card>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {BCard, BButton} from 'bootstrap-vue';
import showdown from 'showdown';

export default defineComponent({
  name: 'LandingPage',
  components: {BCard, BButton},
  methods: {
    goToConfiguration() {
      this.$router.push({name: 'Configuration'});
    },
    md2html: (md) => (new showdown.Converter()).makeHtml(md),
  },
  mounted() {
    if (this.$segment) {
      this.$segment.track('[FBK] PS Social with Facebook & Instagram installed', {
        module: 'ps_facebook',
      });
    }
  },
});
</script>
