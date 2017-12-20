<template>
  <carousel 
    :paginationEnabled="false"
    :navigationEnabled="true"
    :perPage="4">
    <slide v-for="product in products" :key="product.slug">
      <product-preview :product="product"></product-preview>
    </slide>
  </carousel>
</template>

<script>
import { Carousel, Slide } from 'vue-carousel';
import ProductPreview from '@/components/product/ProductPreview';

export default {
  props: ['products'],
  components: {
    Carousel,
    Slide,
    'product-preview': ProductPreview,
  },
};
</script>

<style>

</style>
