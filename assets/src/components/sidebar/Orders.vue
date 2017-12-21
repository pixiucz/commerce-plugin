<template>
  <b-container class="mt-3">
    <div v-if="!showingDetail">
      <h1 class="text-center"> {{ $t('sidebar.orders.title')}} <span @click="closeSidebar" class="pull-right pointer">></span></h1>
      <div class="mt-3">
        <div v-if="!isLoggedIn">
          <a class="login-link" href="javascript:;" @click="redirectToLogin"> {{ $t('sidebar.orders.noLogin') }} </a>
        </div>
        <div v-else> 
          <order-list 
            v-for="order in orders" 
            :key="order.id" 
            :order="order"
            v-on:showOrderDetail="showOrderDetail"
          >
          </order-list>
        </div>
      </div>
    </div>
    <div v-else>
      <h1 class="text-center">
        <span @click="stopShowingDetail" class="pull-left pointer">
           < 
        </span> 
        {{ $t('sidebar.orders.titleDetail') }} #{{ detailOrder.id }} 
        <span @click="closeSidebar" class="pull-right pointer">
          >
        </span>
      </h1>
      <order-detail :order="detailOrder"></order-detail>
    </div>
  </b-container>
</template>

<script>
  import OrderList from '@/components/order/OrderList';
  import OrderDetail from '@/components/order/OrderDetail';

  export default {
    name: 'orders',
    components: {
      'order-list': OrderList,
      'order-detail': OrderDetail,
    },
    data() {
      return ({
        showingDetail: false,
        detailOrder: {},
      });
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
      showOrderDetail(orderId) {
        this.detailOrder = this.orders.find(order => order.id === orderId);
        this.showingDetail = true;
      },
      stopShowingDetail() {
        this.showingDetail = false;
        this.detailOrder = {};
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

