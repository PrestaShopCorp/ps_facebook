<template>
  <b-card
    no-body
    class="category-matched d-flex flex-row flex-align-center"
  >
    <div>
      <b-card-header class="d-flex">
        {{ $t('catalog.summaryPage.categoryMatching.title') }}
      </b-card-header>
      
      <b-card-body class="d-flex justify-content-start">
        <div class="text-category-mapping">
          {{ $t('catalog.summaryPage.categoryMatching.description') }}
          <div
          v-if="matchingProgress.matchingDone"
            class="text-progress-bar-status mt-2"
          >
          {{ $t('catalog.summaryPage.categoryMatching.progress', {
              current: matchingProgress.matchingProgress.matched,
              total: matchingProgress.matchingProgress.total,
            }) }}
          </div>
        </div>
      </b-card-body>
    </div>
    <b-card-body>
      <b-button
        :variant="matchingProgress.matchingDone ? 'outline-primary': 'primary'"
        class="ml-auto text-nowrap mt-2"
        :disabled="!active"
        @click="goToCategoryMatchingPage"
      >
        {{ matchingProgress.matchingDone
          ? $t('cta.manageCategoryMatching')
          : $t('cta.setupCategoryMatching') }}
      </b-button>
    </b-card-body>
  </b-card>
</template>

<script lang="ts">
import {PropType, defineComponent} from 'vue';
import {CategoryMatchingStatus} from '@/store/modules/catalog/state';
import CatalogTabPages from '@/components/catalog/pages';

export default defineComponent({
  name: 'CategoryMatching',
  props: {
    matchingProgress: {
      type: Object as PropType<CategoryMatchingStatus>,
      required: true,
    },
    active: {
      type: Boolean,
      required: true,
    },
  },
  methods: {
    goToCategoryMatchingPage() {
      this.$router.push({
        name: CatalogTabPages.categoryMatchingView,
      });
    },
  },
});
</script>
