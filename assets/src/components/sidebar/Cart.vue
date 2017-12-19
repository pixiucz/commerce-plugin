<template>
  <b-container>
    <h1 class="text-center">Košík</h1>
    <product-list 
      v-for="item in items" 
      :key="item.product.slug"
      :item="item"
    ></product-list>
  </b-container>
</template>

<script>
  import ProductList from '@/components/product/ProductList';

  export default {
    name: 'cart',
    components: {
      'product-list': ProductList,
    },
    computed: {
      items() {
        return this.$store.getters.getCartItems;
      },
    },
  };
</script>
