<template>
  <b-row class="product-list">
    <b-col cols="4">
      <product-image :product="item.product"></product-image>
    </b-col>
    <b-col cols="8 to-upper mt-2">
      <button v-if="!readOnly" type="button" class="close" aria-label="Close">
        <i class="el-icon-close" @click="removeItem(item)"></i>
      </button>
      <h5 @click="showProduct(item.product)" style="cursor: pointer;"> {{ name }} </h5>
      <h6> {{ (item.product.price * item.amount).toFixed(2) | price }} <i class="light"> {{ $t('other.withDPH') }} </i> </h6>
      <h6 v-if="!readOnly" class="dark-grey"> {{ (priceNoTax * item.amount).toFixed(2) | price }} {{ $t('other.withoutDPH') }} </h6>
      <el-input-number :disabled="readOnly" :min="1" v-model="item.amount" @change="changeAmount"></el-input-number>
    </b-col>
  </b-row>
</template>

<script>
import ProductImage from '@/components/product/ProductImage';
import ProductInCartMixin from '@/mixins/ProductInCartMixin';

export default {
  props: ['item', 'readOnly'],
  components: {
    'product-image': ProductImage,
  },
  methods: {
    changeAmount(newValue) {
      this.handleAmountChange(this.item, newValue);
    },
  },
  mixins: [ProductInCartMixin],
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
