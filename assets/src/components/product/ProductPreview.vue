<template>
    <b-card class="hoverable-card">
        <router-link :to="{ name: 'detail', params: { slug: product.slug }}">
                <b-img v-if="product.primary_picture" :src="product.primary_picture.path" fluid />
                <b-img v-else fluid src="http://tz.pixiu.cz/storage/app/uploads/public/595/bab/3d2/595bab3d23cec055399992.png" />
            <div class="card-content">
                <h3 class="mt-1 to-upper">
                    {{ product.product_name }}
                </h3>
            
                <p class="pb-1 mb-0 price">
                    {{ product.price | price }} <i class="light">s DPH</i> <br>
                    {{ priceNoTax | price }} <i class="light">bez DPH</i>
                </p>
            </div>
        </router-link>
    </b-card>
</template>

<script>
import { priceWithoutTax } from '@/helpers';

export default {
  props: ['product'],
  computed: {
    priceNoTax() {
      return priceWithoutTax(this.product.price, this.product.tax_rate);
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
