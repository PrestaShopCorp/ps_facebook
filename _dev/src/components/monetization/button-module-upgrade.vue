<template>
  <b-button
    class="mx-1 mt-3 mt-md-0 ml-md-0 mx-md-1 text-nowrap"
    :variant="upgradeTriggered ? 'outline-primary' : 'primary'"
    @click="triggerUpgrade"
    :disabled="upgradeTriggered"
  >
    <div
      v-if="upgradeTriggered"
      class="spinner small-text mx-4"
    />
    <span
      v-else
    >
      {{ $t('cta.upgrade') }}
    </span>
  </b-button>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'ButtonModuleUpgrade',
  data() {
    return {
      upgradeTriggered: false as boolean,
    };
  },
  methods: {
    async triggerUpgrade(): Promise<void> {
      this.upgradeTriggered = true;
      const actionSucceeded = await this.$store.dispatch('app/TRIGGER_MODULE_MANAGER_ACTION', {
        module: 'ps_facebook',
        action: 'upgrade',
      });

      if (actionSucceeded) {
        window.location.reload();
        return;
      }

      this.upgradeTriggered = false;
      // Todo: display a message about a failed upgrade
    },
  },
});
</script>
