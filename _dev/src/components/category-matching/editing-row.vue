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
  <b-tr :class="categoryStyle">
    <b-td @click="getCurrentRow(shopCategoryId)">
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
        v-if="loading === false"
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
      <div
        v-if="error"
        class="error"
        :title="error"
      >
        <i class="material-icons">error</i>
      </div>
    </b-td>
  </b-tr>
</template>
<script lang="ts">
import {defineComponent} from '@vue/composition-api';
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
      this.$emit('onCategoryChanged', this.shopCategoryId);
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
        fbSubcategoryName: subcategoryName,
        propagate: !!this.currentPropagation,
      };
      this.$emit('onCategoryMatched', result);
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
    categoryStyle(val) {
      this.categoryStyle = val;
    },
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
  },
});
</script>
<style lang="scss" scoped>
  .spinner {
    color: #fff;
    background-color: #fff;
    width: 1.4rem;
    height: 1.4rem;
    border-radius: 2.5rem;
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
  }

  .saved {
    animation: temporary 3s linear normal;
    opacity: 0;
    width: 1.4rem;
    height: 1.4rem;
    display: inline-block;

    & > * {
      zoom: 0.35;
    }
  }

  .checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #70B580;
    fill: none;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
  }

  .checkmark {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: block;
    stroke-width: 2;
    stroke: #fff;
    stroke-miterlimit: 10;
    margin: 10% auto;
    box-shadow: inset 0px 0px 0px #70B580;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
  }

  .checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
  }

  @keyframes stroke {
    100% {
      stroke-dashoffset: 0;
    }
  }
  @keyframes scale {
    0%, 100% {
      transform: none;
    }
    50% {
      transform: scale3d(1.1, 1.1, 1);
    }
  }
  @keyframes fill {
    100% {
      box-shadow: inset 0px 0px 0px 30px #70B580;
    }
  }
  @keyframes temporary {
    0%, 100% {
      opacity: 0;
    }
    1%, 90% {
      opacity: 1;
    }
  }

  .error {
    font-family: Material Icons;
    font-weight: 400;
    font-style: normal;
    font-size: 24px;
    font-size: 1.5rem;
    display: inline-block;
    line-height: 1;
    color: #c05c67;
  }
  .opened {
    td:first-child:before {
      font-family: Material Icons!important;
      font-weight: 400!important;
      font-style: normal!important;
      font-size: 24px!important;
      font-size: 1.5rem!important;
      line-height: 1!important;
      text-transform: none!important;
      letter-spacing: normal!important;
      word-wrap: normal!important;
      white-space: nowrap!important;
      direction: ltr!important;
      -webkit-font-smoothing: antialiased!important;
      text-rendering: optimizeLegibility!important;
      -moz-osx-font-smoothing: grayscale!important;
      font-feature-settings: "liga"!important;
      content: "expand_more"!important;
      border: none!important;
      display: inline-block!important;
      vertical-align: middle!important;
      width: auto!important;
      line-height: 0!important;
    }
    td:first-child {
      cursor: pointer!important;
    }
  }
  .closed {
    td:first-child:before {
      font-family: Material Icons!important;
      font-style: normal!important;
      font-size: 15px!important;
      font-size: 1.5rem!important;
      line-height: 1!important;
      text-transform: none!important;
      letter-spacing: normal!important;
      word-wrap: normal!important;
      white-space: nowrap!important;
      direction: ltr!important;
      -webkit-font-smoothing: antialiased!important;
      text-rendering: optimizeLegibility!important;
      -moz-osx-font-smoothing: grayscale!important;
      font-feature-settings: "liga"!important;
      content: "expand_less"!important;
      transform: rotate(90deg)!important;
      border: none!important;
      display: inline-block!important;
      vertical-align: middle!important;
      width: auto!important;
      line-height: 0!important;
    }
    td:first-child {
      cursor: pointer!important;
    }
  }

  .array-tree-lvl-2 {
    td:first-child {
      padding-left:40px!important;
    }
  }
  .array-tree-lvl-3 {
    td:first-child {
      padding-left:80px!important;
    }
  }

  .propagate > * {
    zoom: 1.2!important;
  }
</style>
