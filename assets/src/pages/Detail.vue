<template>
  <b-container v-if="product" v-loading="isLoading">
    <div class="category-header">
      <p class="margin-top4 margin-bottom4 text-color">
        <router-link :to="{ name: 'category', params: { slug: product.categories[0][0].slug }}">
          {{ product.categories[0][0].name }}
        </router-link> 
        &gt; {{ name }}
      </p>
    </div>
    <b-row>
        <b-col md="5" class="text-center">
            <b-img v-if="product.primary_picture" fluid :src="product.primary_picture.path" />
            <b-img v-else fluid src="http://tz.pixiu.cz/storage/app/uploads/public/595/bab/3d2/595bab3d23cec055399992.png" />
        </b-col>
        <b-col md="7">
            <h1 class="big margin-top-9">{{ name }}</h1>
            <p class="text-color margin-bottom3" v-html="product.short_description"></p>
            <b-row>
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
</template>

<script>
  import { getProduct } from '@/api';
  import { priceWithoutTax, getFullProductName } from '@/helpers';

  export default {
    name: 'detail',
    props: ['slug'],
    data() {
      return {
        product: null,
        amount: 1,
        isLoading: true,
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
      }
    },
    watch: {
      $route: 'reuseComponent',
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
