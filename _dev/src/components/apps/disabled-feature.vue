<template>
  <li>
    <b-card no-body>
      <b-card-body>
        <div class="d-flex">
          <img
            class="mr-2 align-self-center logo"
            src="../../assets/logo.png"
            width="50"
          >
          <div class="description align-self-center flex-grow-1 pl-3 pr-2">
            <h3>
              SOME NAME
            </h3>
            <p class="small-text text-muted">
              DESC
            </p>
          </div>
          <b-button
            variant="primary"
            class="ml-4 align-self-center"
          >
            {{ $t('integrate.add') }}
          </b-button>
        </div>
      </b-card-body>
    </b-card>
  </li>
</template>

<script>
export default {
  name: 'DisabledFeature',
};
</script>

<style lang="scss" scoped>
  .logo {
    float: left;
    display: block;
  }

  .description {
    display: table-cell;
  }

  .flex-grow-1 {
    flex-grow:1
  }
</style>
