<template>
  <b-row class="mt-3">
    <b-col cols="7">
      <span class="order-date"> {{ date }} </span> <span class="order-status"> {{ order.status }} </span> <br>
      <span class="order-price">24.50 € s DPH</span>
    </b-col>
    <b-col cols="5">
      <el-button class="pull-right" size="mini">Default</el-button>
      <el-button class="pull-right" size="mini">Default</el-button>
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
