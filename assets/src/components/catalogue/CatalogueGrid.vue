<template>
  <div>
    <b-container>
        <b-row v-if="products">
            <b-col cols="12" md="3" sm="4" v-for="product in products" :key="product.id">
                <product-preview :product="product"></product-preview>
            </b-col>
        </b-row>
    </b-container>
  </div>
</template>

<script>
import ProductPreview from '../product/ProductPreview';

export default {
  props: ['products'],
  components: {
    'product-preview': ProductPreview,
  },
};
</script>

<style>

</style>
