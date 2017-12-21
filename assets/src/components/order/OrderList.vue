<template>
  <b-row class="mt-3">
    <b-col cols="5">
      <span class="order-date"> {{ date }} </span> <span class="order-status"> {{ order.status }} </span> <br>
      <span class="order-price">{{ sum | price }} {{ $t('other.withDPH') }}</span>
    </b-col>
    <b-col cols="7">
      <el-button @click="showDetail" class="pull-right" type="primary" size="mini"><i class="el-icon-view"></i> {{ $t('sidebar.orders.buttons.showMore ') }}</el-button>
      <el-button @click="cancelOrder" class="pull-right" type="primary" size="mini"><i class="el-icon-delete"></i> {{ $t('sidebar.orders.buttons.cancel') }}</el-button>
    </b-col>
  </b-row>
</template>

<script>
import moment from 'moment';

export default {
  name: 'OrderList',
  props: ['order'],
  computed: {
    date() {
      return moment(this.order.created_at).locale('sk').format('L');
    },
    sum() {
      let sum = 0;
      this.order.variants.forEach((product) => {
        sum += product.pivot.price * product.pivot.quantity;
      });
      return sum;
    },
  },
  methods: {
    cancelOrder() {
      this.$confirm(this.$t('sidebar.orders.dialog.cancel'))
          .then(() => {
            this.$message('Neni implementovano 😱');
          })
          .catch(() => {});
    },
    showDetail() {
      this.$emit('showOrderDetail', this.order.id);
    },
  },
};
</script>

<style>
  .order-status {
    font-size: 0.5em;
    border: white solid 1px;
    padding: 3px;
    text-transform: uppercase;
  }

  .order-date {
    font-size: 1.25em;
    text-transform: uppercase;
  }

  .order-price {
    font-size: 1em;
    text-transform: uppercase;
  }

</style>
