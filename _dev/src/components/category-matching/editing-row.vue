<!--**
 * 2007-2021 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <b-tr
    class="editing-row"
    :class="categoryStyle"
  >
    <b-td
      @click="getCurrentRow(shopCategoryId)"
      :class="isMainCategory"
    >
      <slot />
    </b-td>
    <b-td>
      <div
        v-if="initialPropagation === true || initialPropagation === false"
        class="propagate"
      >
        <b-checkbox
          :id="`propagation-${shopCategoryId}`"
          :checked="currentPropagation"
          @change="changePropagation($event, shopCategoryId)"
        />
      </div>
    </b-td>
    <b-td>
      <category-autocomplete
        :language="language"
        :shop-category-id="shopCategoryId"
        :initial-category-name="currentCategoryName"
        :initial-category-id="currentCategoryId"
        :autocompletion-api="autocompletionApi"
        @onCategorySelected="categoryChanged"
      />
    </b-td>
    <b-td>
      <category-autocomplete
        :key="currentCategoryId"
        :language="language"
        :shop-category-id="shopCategoryId"
        :initial-category-name="currentSubcategoryName"
        :initial-category-id="currentSubcategoryId"
        :parent-category-id="currentCategoryId"
        :autocompletion-api="autocompletionApi"
        :disabled="!currentCategoryId"
        @onCategorySelected="subcategoryChanged"
      />
    </b-td>
    <b-td>
      <div
        v-if="loading === true"
        class="spinner"
      />
      <div
        v-else-if="error"
        class="error"
        :title="error"
      >
        <i class="material-icons material-icons-round">error</i>
      </div>
      <div
        v-else-if="loading === false"
        class="saved"
      >
        <svg
          class="checkmark"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 52 52"
        >
          <circle
            class="checkmark__circle"
            cx="26"
            cy="26"
            r="25"
            fill="none"
          />
          <path
            class="checkmark__check"
            fill="none"
            stroke-width="4"
            d="M14.1 27.2l7.1 7.2 16.7-16.8"
          />
        </svg>
      </div>
    </b-td>
  </b-tr>
</template>
<script lang="ts">
import {defineComponent} from 'vue';
import {
  BTr,
  BTd,
} from 'bootstrap-vue';
import CategoryAutocomplete from './category-autocomplete.vue';

export default defineComponent({
  name: 'EditingRow',
  components: {
    BTr,
    BTd,
    CategoryAutocomplete,
  },
  mixins: [],
  props: {
    language: {
      type: String,
      required: false,
      default: 'en-US',
    },
    shopCategoryId: {
      type: String,
      required: true,
    },
    initialCategoryName: {
      type: String,
      required: false,
      default: null,
    },
    initialCategoryId: {
      type: Number,
      required: false,
      default: null,
    },
    categoryStyle: {
      type: String,
      required: true,
    },
    isMainCategory: {
      type: String,
      required: true,
    },
    initialSubcategoryName: {
      type: String,
      required: false,
      default: null,
    },
    initialSubcategoryId: {
      type: Number,
      required: false,
      default: null,
    },
    initialPropagation: {
      type: Boolean,
      required: false,
      default: undefined,
    },
    saveMatchingCallback: {
      type: Function,
      required: false,
      default: () => new Promise((resolve, reject) => {
        setTimeout(() => reject(new Error('No callback given!')), 1000);
      }),
    },
    autocompletionApi: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      currentCategoryName: this.initialCategoryName as string | null,
      currentCategoryId: +this.initialCategoryId as number | null,
      currentSubcategoryName: this.initialSubcategoryName as string | null,
      currentSubcategoryId: +this.initialSubcategoryId as number | null,
      currentPropagation: !!this.initialPropagation,
      loading: null as boolean | null, // init at null : no green checkmark
      error: null,
    };
  },
  methods: {
    changePropagation(checked, shopCategoryId) {
      this.currentPropagation = checked;
      this.$emit('propagationClicked', shopCategoryId, checked);

      if (checked === true
        && this.currentSubcategoryId !== 0
        && this.currentCategoryId !== 0) {
        const result = {
          shopCategoryId: this.shopCategoryId,
          fbCategoryId: this.currentCategoryId,
          fbCategoryName: this.currentCategoryName.replace('&', '-'),
          fbSubcategoryId: this.currentSubcategoryId,
          fbSubcategoryName: this.currentSubcategoryName.replace('&', '-'),
          propagate: !!this.currentPropagation,
        };
        this.triggerSaveOfCategoryMatching(result);
      }
    },
    getCurrentRow(categoryID) {
      this.$emit('rowClicked', categoryID);
    },
    categoryChanged(categoryId, categoryName) {
      if (this.currentCategoryId !== categoryId) {
        this.currentCategoryId = categoryId;
        this.currentCategoryName = categoryName;
        this.currentSubcategoryId = null;
        this.currentSubcategoryName = null;
      }
      const checkbox = document.getElementById(`propagation-${this.shopCategoryId}`);

      if (checkbox) {
        checkbox.focus();
      }
      if (categoryId) {
        return;
      }

      // Trigger save when category is unset
      const result = {
        shopCategoryId: this.shopCategoryId,
        fbCategoryId: this.currentCategoryId,
        fbCategoryName: this.currentCategoryName.replace('&', '-'),
        fbSubcategoryId: null,
        fbSubcategoryName: null,
        propagate: !!this.currentPropagation,
      };
      this.triggerSaveOfCategoryMatching(result);
    },
    subcategoryChanged(subcategoryId, subcategoryName) {
      this.loading = true;
      this.error = null;
      this.currentSubcategoryId = subcategoryId;
      this.currentSubcategoryName = subcategoryName;
      const result = {
        shopCategoryId: this.shopCategoryId,
        fbCategoryId: this.currentCategoryId,
        fbCategoryName: this.currentCategoryName.replace('&', '-'),
        fbSubcategoryId: subcategoryId,
        fbSubcategoryName: subcategoryName.replace('&', '-'),
        propagate: !!this.currentPropagation,
      };
      this.$emit('onCategoryMatched', result);
      this.triggerSaveOfCategoryMatching(result);
    },
    triggerSaveOfCategoryMatching(result) {
      this.saveMatchingCallback(result)
        .then(() => {
          this.loading = false;
          this.error = null;
        })
        .catch((error) => {
          this.loading = null;
          this.error = error;
        });
    },
  },
  watch: {
    initialCategoryName(newVal) {
      this.currentCategoryName = newVal;
    },
    initialSubcategoryName(newVal) {
      this.currentSubcategoryName = newVal;
    },
    initialCategoryId(newVal) {
      this.currentCategoryId = newVal;
    },
    initialSubcategoryId(newVal) {
      this.currentSubcategoryId = newVal;
    },
    initialPropagation(newVal) {
      this.currentPropagation = newVal;
    },
  },
});
</script>
<style lang="scss" scoped>
  .spinner {
    width: 1.4rem!important;
    height: 1.4rem!important;
  }
</style>
