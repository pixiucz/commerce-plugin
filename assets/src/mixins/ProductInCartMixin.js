import { priceWithoutTax, getFullProductName } from '@/helpers';

const ProductInCartMixin = {
  methods: {
    handleAmountChange(item, value) {
      this.$store.dispatch('CHANGE_ITEM_AMOUNT', {
        slug: item.product.slug,
        newAmount: value,
      });
    },
    removeItem(item) {
      this.$store.dispatch('REMOVE_ITEM', item.product.slug);
    },
    showProduct(product) {
      this.$router.push({ name: 'detail', params: { slug: product.slug } });
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
