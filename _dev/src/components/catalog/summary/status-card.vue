<template>
  <div
    class="ps_gs-productfeed__products-status card col mx-1 mb-1"
  >
    <b-skeleton-wrapper
      :loading="loading"
    >
      <template #loading>
        <div
          class="d-flex flex-column align-items-center p-3"
        >
          <b-skeleton
            width="70%"
            class="mt-2 mb-3"
          />
          <b-skeleton
            width="100%"
          />
          <b-skeleton
            width="100%"
            class="mb-2"
          />
          <b-skeleton
            width="20%"
            height="2.5em"
          />
        </div>
      </template>

      <div
        class="d-flex p-3"
      >
        <span
          class="material-icons material-icons-round ps_gs-fz-20 mt-1 mr-1
            ps_gs-productfeed__products-status__icon"
          :class="`text-${status.variant}`"
          data-test-id="pf-status-icon"
        >
          {{ status.icon }}
        </span>
        <div
          class="d-flex flex-column align-items-start"
        >
          <span
            class="d-flex flex-column
            ps_gs-fz-16 font-weight-500 pt-1 pb-2"
            data-test-id="pf-status-title"
          >
            {{ status.title }}
          </span>
          <span
            class="pb-3"
            data-test-id="pf-status-description"
          >
            {{ status.description }}
          </span>
          <b-card
            :border-variant="badgeBorderVariant"
            :text-variant="badgeTextVariant"
            :bg-variant="badgeBackgroundVariant"
            class="ps_gs-productfeed__badge mt-auto"
            body-class="px-3"
          >
            {{ badgeValue }}
          </b-card>
          <b-link
            v-if="status.link"
            v-bind="status.link"
            class="stretched-link external_link-no_icon"
            @click="onLinkClick"
          />
        </div>
      </div>
    </b-skeleton-wrapper>
  </div>
</template>

<script lang="ts">
import {PropType, defineComponent} from 'vue';

export type StatusCardParameters = {
  id: string,
  title: string;
  description: string;
  icon: string;
  value: string | null;
  reverseColors: boolean;
  variant: string;
  link?: {
    to?: {
      name: string;
    };
    href?: string;
    target?: string;
  };
};

export default defineComponent({
  props: {
    status: {
      type: Object as PropType<StatusCardParameters>,
      required: true,
    },
    loading: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    badgeValue(): string {
      return this.status.value ?? '--';
    },
    badgeBorderVariant(): string|null {
      return this.status.variant;
    },
    badgeTextVariant(): string|null {
      if (this.status.reverseColors) {
        return 'white';
      }
      return this.status.variant;
    },
    badgeBackgroundVariant(): string|null {
      if (this.status.reverseColors) {
        return this.status.variant;
      }
      return 'white';
    },
  },
  methods: {
    onLinkClick(): void {
      this.$segment.track(`[FBK] Product feed - Click on Status card "${this.status.id}"`, {
        module: 'ps_facebook',
      });
    },
  },
});
</script>
