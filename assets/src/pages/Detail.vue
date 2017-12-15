<template>
  <b-container v-if="product">
    <div class="category-header">
      <p class="margin-top4 margin-bottom4 text-color">
        <router-link :to="{ name: 'category', params: { slug: product.categories[0][0].slug }}">
          {{ product.categories[0][0].name }}
        </router-link> 
        &gt; {{ product.product_name }}
      </p>
    </div>
    <b-row>
        <b-col md="5">
            <b-img v-if="product.primary_picture" fluid :src="product.primary_picture.path" />
            <b-img v-else fluid src="http://tz.pixiu.cz/storage/app/uploads/public/595/bab/3d2/595bab3d23cec055399992.png" />
        </b-col>
        <b-col md="7">
            <h1 class="big margin-top-9">{{ product.product_name }}</h1>
            <p class="text-color margin-bottom3" v-html="product.short_description"></p>
            <b-row>
                <b-col md="12" class="text-center">
                    <el-input-number v-model="amount" :min="1" :max="50"></el-input-number>             
                    <el-button>Přidat do košíka</el-button>
                </b-col>
            </b-row>              
        </b-col>
    </b-row>
    <b-row>
      <b-col md="12">
        {{ product.long_description }}
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
  import { getProduct } from '../api';

  export default {
    name: 'detail',
    props: ['slug'],
    data() {
      return {
        product: null,
        amount: 1,
      };
    },
    async created() {
      const product = await getProduct(this.slug);
      this.product = product;
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

</style>
