<template>
  <ul>
    <slot />
  </ul>
</template>

<script>
export default {
  name: 'AppList',
};
</script>

<style lang="scss" scoped>
  ul {
    padding: 15px;
  }
</style>
