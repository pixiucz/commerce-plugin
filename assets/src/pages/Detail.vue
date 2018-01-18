<template>
  <div v-loading="isLoading">
    <b-container v-if="product">
      <div class="category-header">
        <p class="margin-top4 margin-bottom4 text-color">
          <router-link v-if="categorySlug" :to="{ name: 'category', params: { slug: categorySlug }}">
            {{ categoryName }}
          </router-link> 
          &gt; {{ name }}
        </p>
      </div>
      <b-row>
          <b-col md="5" class="text-center">
              <product-image :product="product"></product-image>
          </b-col>
          <b-col md="7">
              <h1 class="big margin-top-9">{{ name }}</h1>
              <p class="text-color margin-bottom3" v-html="product.short_description"></p>
              <el-select v-if="product.otherVariants.length > 1" v-model="variantDropdownValue" :placeholder="$t('pages.detail.misc.productVariants')" style="width: 100%">
                <el-option
                  v-for="variant in product.otherVariants"
                  :key="variant.slug"
                  :value="variant.slug"
                  :label="getFullVariantName(variant)"
                  style="height: 75px; width: 100%;"
                  class="mt-1">
                    <b-row>
                      <b-col md="3">
                        <product-image :product="variant"></product-image>
                      </b-col>
                      <b-col md="9">
                        {{ getFullVariantName(variant) }} <br>
                        {{ variant.price | price }}
                      </b-col>
                    </b-row> 
                </el-option>
              </el-select>
              <b-row class="mt-3">
                  <b-col md="12" class="text-center">
                      <div>
                        <h4> {{ product.price | price }} {{ $t('other.withDPH')}} </h4>
                        <p class="dark-grey"> {{ priceNoTax | price }} {{ $t('other.withoutDPH') }}</p>
                      </div>
                      <el-input-number v-model="amount" :min="1" :max="50"></el-input-number>             
                      <el-button class="add-button" @click.stop="addToCart"> {{ $t('pages.detail.buttons.addToCart') }} </el-button>
                      <p v-if="inCart > 0"> {{ $t('pages.detail.misc.inBasket') }}: <b>{{ inCart }} {{ $t('other.piece') }} </b> </p>
                  </b-col>
              </b-row>              
          </b-col>
      </b-row>
      <b-row>
        <b-col md="12">
          {{ product.long_description }}
          <br>
          <b-table small v-if="product.specifications" striped hover :items="product.specifications">
            <template slot="HEAD_name" slot-scope="data">
            {{ $t('pages.detail.misc.name') }}
            </template>
            <template slot="HEAD_value" slot-scope="data">
              {{ $t('pages.detail.misc.value') }}
            </template>
          </b-table>
        </b-col>
      </b-row>
    </b-container>
    <div style="height: 100%; width: 100%; padding-top: 45vh;" v-else>
    </div>
  </div>
</template>

<script>
  import { getProduct } from '@/api';
  import { priceWithoutTax, getFullProductName } from '@/helpers';
  import ProductImage from '@/components/product/ProductImage';

  export default {
    name: 'detail',
    props: ['slug'],
    components: {
      'product-image': ProductImage,
    },
    data() {
      return {
        product: null,
        amount: 1,
        isLoading: true,
        variantDropdownValue: '',
      };
    },
    async created() {
      const product = await getProduct(this.slug);
      this.product = product;
      this.isLoading = false;
    },
    methods: {
      addToCart() {
        const item = {
          amount: this.amount,
          product: this.product,
        };
        this.$store.dispatch('ADD_TO_CART', item);

        this.amount = 1;

        this.openSidebar('Cart');
      },
      async reuseComponent() {
        this.isLoading = true;
        const product = await getProduct(this.slug);
        this.product = product;
        this.isLoading = false;
      },
      getFullVariantName(variant) {
        return getFullProductName(variant);
      },
    },
    computed: {
      inCart() {
        if (this.product) {
          const item = this.$store.getters.getItemFromCart(this.product.slug);
          if (item) {
            return item.amount;
          }
        }
        return 0;
      },
      priceNoTax() {
        return priceWithoutTax(this.product.price, this.product.tax_rate);
      },
      name() {
        return getFullProductName(this.product);
      },
      categoryName() {
        if (this.product.categories) {
          return this.product.categories[0][0].name;
        }
        return ' - ';
      },
      categorySlug() {
        if (this.product.categories) {
          return this.product.categories[0][0].slug;
        }
        return false;
      },
    },
    watch: {
      $route: 'reuseComponent',
      variantDropdownValue(slug) {
        this.$router.push({ name: 'detail', params: { slug } });
      },
    },
  };
</script>

<style scoped>
  .category-header {
    margin-top: 0.5em;
    margin-bottom: 2em;
  }

  .row {
    margin-bottom: 2em;
  }

  .add-button {
    background-color: #24272C;
    color: #ffffff;
    border: none;
    border-radius: 0px;
    padding: 10 18 10 18px;
    font-size: 15px;
  }

</style>
