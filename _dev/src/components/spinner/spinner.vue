<template>
  <div class="spinner" />
</template>

<script>
import {defineComponent} from '@vue/composition-api';

export default defineComponent({
  name: 'Spinner',
});
</script>

<style lang="scss" scoped>
  .spinner {
    width: 8rem !important;
    height: 8rem !important;
    position: relative;
    left: calc(50% - 4rem);
    top: 6rem;
  }
</style>
