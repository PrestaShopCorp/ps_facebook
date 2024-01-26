<template>
  <li class="ps_fb_featurecard">
    <b-card no-body>
      <b-card-body
        class="p-3"
      >
        <div class="d-flex">
          <img
            class="mr-2 align-self-center logo logo-disabled d-none d-md-block"
            :src="imageUrl"
          >
          <div class="description align-self-top flex-grow-1 pl-3 pr-2">
            <h3>
              {{ $t(`integrate.features.${name}.name`) }}
            </h3>
            <p>
              {{ $t(`integrate.features.${name}.description`) }}
            </p>
          </div>
          <b-button
            variant="outline-primary disabled"
            class="ml-4 align-self-center"
          >
            {{ $t(`integrate.features.${name}.addButton`) }}
          </b-button>
        </div>
      </b-card-body>
    </b-card>
  </li>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {BCard, BCardBody} from 'bootstrap-vue';

export default defineComponent({
  name: 'UnavailableFeature',
  components: {
    BCard,
    BCardBody,
  },
  computed: {
    imageUrl(): string {
      return new URL(`/src/assets/${this.name}.svg`, import.meta.url).href;
    },
  },
  props: {
    name: {
      type: String,
      required: false,
      default: () => '',
    },
  },
});
</script>
