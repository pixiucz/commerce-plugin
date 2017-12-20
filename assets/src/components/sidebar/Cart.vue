<template>
  <b-container class="sidebar-cart mt-3">
    <h1 class="text-center">Košík <span @click="closeSidebar" class="pull-right pointer">></span></h1>
    <product-list 
      v-for="item in items" 
      :key="item.product.slug"
      :item="item"
    ></product-list>
    <hr>
   
    <b-row>
      <b-col cols="3">
        <h2> SPOLU </h2>
      </b-col>
      <b-col offset="5" cols="4">
        <p> {{ itemsSum.withTax }} € s DPH <br>
        <span class="dark-grey">{{ itemsSum.withoutTax }} € bez DPH</span> </p>
      </b-col>
    </b-row>
    <el-button class="send-order-btn">
      Objednať
    </el-button>
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
      itemsSum() {
        return this.$store.getters.getCartSum;
      },
    },
  };
</script>

<style scoped>
  .send-order-btn {
    width: 100%;
  }
</style>
