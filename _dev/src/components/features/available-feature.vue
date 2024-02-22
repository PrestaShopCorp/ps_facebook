<template>
  <li class="ps_fb_featurecard">
    <b-card no-body>
      <b-card-body
        class="p-3"
      >
        <div class="d-flex">
          <img
            class="mr-2 align-self-center logo d-none d-md-block"
            :src="imageUrl"
            :alt="$t(`integrate.features.${name}.name`)"
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
            variant="outline-primary"
            class="ml-4 align-self-center"
            @click="onSales(name)"
            :href="manageRoute[name] || manageRoute.default"
            target="_blank"
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

export default defineComponent({
  name: 'AvailableFeature',
  mixins: [],
  props: {
    name: {
      type: String,
      required: false,
      default: () => '',
    },
    manageRoute: {
      type: Object,
      required: true,
    },
    updateFeatureRoute: {
      type: String,
      required: false,
      default: window.psFacebookUpdateFeatureRoute,
    },
  },
  computed: {
    imageUrl(): string {
      return new URL(`/src/assets/${this.name}.svg`, import.meta.url).href;
    },
  },
  methods: {
    onSales(name) {
      this.$segment.track(`[FBK] Add CTA - ${name}`, {
        module: 'ps_facebook',
      });

      fetch(this.updateFeatureRoute, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
        body: JSON.stringify({featureName: this.name, enabled: true}),
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        if (res.success === false) {
          throw new Error('failed to update feature');
        } else {
          this.$emit('onToggleSwitch', this.name, true);
          this.$store.dispatch('app/FIX_UNREGISTERED_HOOKS');
        }
      }).catch((error) => {
        console.error(error);
      });
    },
  },
});
</script>
