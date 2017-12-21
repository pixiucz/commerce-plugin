<template>
  <b-container class="mt-3">
    <h1 class="text-center"> {{ $t('sidebar.orders.title')}} <span @click="closeSidebar" class="pull-right pointer">></span></h1>
    <div class="mt-3">
      <div v-if="!isLoggedIn">
        <a class="login-link" href="javascript:;" @click="redirectToLogin"> {{ $t('sidebar.orders.noLogin') }} </a>
      </div>
      <div v-else> 
        <order-list v-for="order in orders" :key="order.id" :order="order"></order-list>
      </div>
    </div>
  </b-container>
</template>

<script>
  import OrderList from '@/components/order/OrderList';

  export default {
    name: 'orders',
    components: {
      'order-list': OrderList,
    },
    computed: {
      isLoggedIn() {
        return this.$store.getters.isLoggedIn;
      },
      orders() {
        return this.$store.getters.getOrders;
      },
    },
    methods: {
      redirectToLogin() {
        this.openSidebar('User');
      },
    },
  };
</script>

<style scoped>
  .login-link {
    color: white;
  }
  .login-link:hover {
    text-decoration: underline;
  }
</style>

