<template>
  <b-row class="product-list">
    <b-col cols="4">
      <b-img v-if="item.product.primary_picture" :src="item.product.primary_picture.path" fluid />
      <b-img v-else fluid src="http://tz.pixiu.cz/storage/app/uploads/public/595/bab/3d2/595bab3d23cec055399992.png" />
    </b-col>
    <b-col cols="8 to-upper mt-2">
      <button v-if="!readOnly" type="button" class="close" aria-label="Close">
        <i class="el-icon-close" @click="removeItem"></i>
      </button>
      <h5 @click="showProduct" style="cursor: pointer;"> {{ name }} </h5>
      <h6> {{ (item.product.price * item.amount).toFixed(2) | price }} <i class="light"> {{ $t('other.withDPH') }} </i> </h6>
      <h6 class="dark-grey"> {{ (priceNoTax * item.amount).toFixed(2) | price }} {{ $t('other.withoutDPH') }} </h6>
      <el-input-number :disabled="readOnly" :min="1" v-model="item.amount" @change="handleAmountChange"></el-input-number>
    </b-col>
  </b-row>
</template>

<script>
import { priceWithoutTax, getFullProductName } from '@/helpers';

export default {
  props: ['item', 'readOnly'],
  methods: {
    handleAmountChange(value) {
      this.$store.dispatch('CHANGE_ITEM_AMOUNT', {
        slug: this.item.product.slug,
        newAmount: value,
      });
    },
    removeItem() {
      this.$store.dispatch('REMOVE_ITEM', this.item.product.slug);
    },
    showProduct() {
      this.$router.push({ name: 'detail', params: { slug: this.item.product.slug } });
      this.$store.commit('SET_SIDEBAR_VISIBLE', false);
    },
  },
  computed: {
    priceNoTax() {
      return priceWithoutTax(this.item.product.price, this.item.product.tax_rate);
    },
    name() {
      return getFullProductName(this.item.product);
    },
  },
};
</script>

<style scoped>
  .close {
    cursor: pointer;
  }

  .product-list {
    margin-top: 20px;
    margin-bottom: 10px;
  }
</style>
