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
    color: #fff;
    background-color: inherit !important;
    width: 8rem !important;
    height: 8rem !important;
    border-radius: 4rem !important;
    border-right-color: #25b9d7;
    border-bottom-color: #25b9d7;
    border-width: .1875rem;
    border-style: solid;
    font-size: 0;
    outline: none;
    display: inline-block;
    border-left-color: #bbcdd2;
    border-top-color: #bbcdd2;
    -webkit-animation: rotating 2s linear infinite;
    animation: rotating 2s linear infinite;
    position: relative;
    left: calc(50% - 4rem);
    top: 6rem;
  }
</style>
