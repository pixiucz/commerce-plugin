<template>
  <b-container v-if="product">
    <p class="margin-top4 margin-bottom4 text-color"><a class="custom-href" href="#">{{ product.categories[0][0].name }}</a> &gt; {{ product.product_name }}</p>
    <b-row>
        <b-col md="5">
            <b-img v-if="product.primary_picture" fluid :src="product.primary_picture.path" />
        </b-col>
        <b-col md="7">
            <h1 class="big margin-top-9">{{ product.product_name }}</h1>
            <p class="text-color margin-bottom3" v-html="product.short_description"></p>
            <b-row>
                <b-col md="5">
                    <el-input-number v-model="amount" :min="1" :max="10"></el-input-number>             
                </b-col>
                <b-col md="7">
                    <el-button>Přidat do košíka</el-button>
                </b-col>
            </b-row>              
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
      console.log(product);
      this.product = product;
    },
  };
</script>
