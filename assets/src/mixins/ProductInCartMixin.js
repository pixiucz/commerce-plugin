import { priceWithoutTax, getFullProductName } from '@/helpers';

const ProductInCartMixin = {
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

export default ProductInCartMixin;
