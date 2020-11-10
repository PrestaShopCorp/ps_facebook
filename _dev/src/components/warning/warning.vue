<template>
  <div
    class="alert medium-alert alert-warning mt-3"
    role="alert"
  >
    <p class="alert-text">
      {{ warningText }}
    </p>
    <slot />
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BCard, BCardBody} from 'bootstrap-vue';

export default defineComponent({
  name: 'Warning',
  components: {
    BCard,
    BCardBody,
  },
  props: {
    warningText: {
      type: String,
      required: true,
    },
  },
});
</script>

<style lang="scss" scoped>
.flex-grow-1 {
  flex-grow:1
}
</style>
