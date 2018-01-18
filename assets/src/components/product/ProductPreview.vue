<template>
    <b-card class="hoverable-card">
        <router-link :to="{ name: 'detail', params: { slug: product.slug }}">
            <product-image :product="product"></product-image>
            <div class="card-content">
                <h3 class="mt-1 to-upper">
                    {{ name }}
                </h3>
            
                <p class="pb-1 mb-0 price">
                    {{ product.price | price }} <i class="light">{{ $t('other.withDPH') }}</i> <br>
                    {{ priceNoTax | price }} <i class="light">{{ $t('other.withoutDPH') }}</i>
                </p>
            </div>
        </router-link>
    </b-card>
</template>

<script>
import { priceWithoutTax, getFullProductName } from '@/helpers';
import ProductImage from '@/components/product/ProductImage';

export default {
  props: ['product'],
  components: {
    'product-image': ProductImage,
  },
  computed: {
    priceNoTax() {
      return priceWithoutTax(this.product.price, this.product.tax_rate);
    },
    name() {
      return getFullProductName(this.product);
    },
  },
};
</script>

<style scoped>
    .card {
        border: none; 
        /* margin: 10px -15px 10px -15px;   */
    }

    a:hover {
        text-decoration: none;
        color: black;
    }

    h3 {
        font-size: 1.2rem;
    }

    .card-content {
        margin-top: 20px;
    }

    .price {
        text-transform: uppercase;
        font-size: 1.2rem;
    }
</style>
