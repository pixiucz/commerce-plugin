<template>
  <b-row>
    <p> Dorucovaci adresa, Fakturacni adresa, Suma, Dalsi info,...</p>
    <product-list v-for="product in order.variants" :key="product.id" :item="{ product, amount: product.quantity }" :readOnly="true"></product-list>
  </b-row>
</template>

<script>
import ProductList from '@/components/product/ProductList';

export default {
  name: 'OrderDetail',
  props: ['order'],
  components: {
    'product-list': ProductList,
  },
};
</script>

<style scoped>

</style>
