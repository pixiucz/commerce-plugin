<template>
  <b-container>
    <div class="newest-products slider">
      <h2> Najnovšie produkty </h2>
      <catalogue-slider 
        :products="products" 
        v-loading="isLoading"></catalogue-slider>
    </div>
    <div class="best-sellers slider">
      <h2> Najpredávanejšie produkty </h2>
      <catalogue-slider :products="products" v-loading="isLoading"></catalogue-slider>
    </div>
  </b-container>
</template>

<script>
  import CatalogueSlider from '@/components/catalogue/CatalogueSlider';

  export default {
    name: 'home',
    components: {
      'catalogue-slider': CatalogueSlider,
    },
    data() {
      return ({
        isLoading: true,
      });
    },
    async created() {
      this.fetchProducts();
    },
    methods: {
      async fetchProducts() {
        this.isLoading = true;
        await this.$store.dispatch('GET_PRODUCTS');
        this.isLoading = false;
      },
    },
    computed: {
      products() {
        return this.$store.getters.getProducts;
      },
    },
  };
</script>

<style scoped>
  .slider {
    margin-top: 3em;
  }

</style>

