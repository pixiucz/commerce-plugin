<template>
  <b-container class="sidebar-cart mt-3">
    <h1 class="text-center"> {{ $t('sidebar.cart.title') }} <span @click="closeSidebar" class="pull-right pointer">></span></h1>
    <product-list 
      v-for="item in items" 
      :key="item.product.slug"
      :item="item"
      :readOnly="false"
    ></product-list>
    <hr>
   
    <b-row>
      <b-col cols="3">
        <h2> {{ $t('sidebar.cart.misc.together') }} </h2>
      </b-col>
      <b-col offset="5" cols="4">
        <p> {{ itemsSum.withTax | price }} {{ $t('other.withDPH') }} <br>
        <span class="dark-grey"> {{ itemsSum.withoutTax | price }} {{ $t('other.withoutDPH') }}</span> </p>
      </b-col>
    </b-row>
    <el-button :disabled="cartEmpty" class="send-order-btn" @click="redirectToCheckout">
      {{ $t('sidebar.cart.buttons.order') }}
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
      cartEmpty() {
        return this.$store.getters.getCartLength === 0;
      },
    },
    methods: {
      redirectToCheckout() {
        this.closeSidebar();
        this.$router.push({ name: 'checkout' });
      },
    },
  };
</script>

<style scoped>
  .send-order-btn {
    width: 100%;
  }
</style>
