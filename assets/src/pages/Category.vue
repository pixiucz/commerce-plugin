<template>
  <div v-if="category">
    <h1> {{ category.name }} </h1>
    <catalogue-grid :products="products"></catalogue-grid>
  </div>
</template>

<script>
  import CatalogueGrid from '../components/catalogue/CatalogueGrid';

  export default {
    name: 'category',
    props: ['slug'],
    components: {
      'catalogue-grid': CatalogueGrid,
    },
    async mounted() {
      await this.fetchProducts();
    },
    computed: {
      category() {
        return this.$store.getters.getCategory(this.slug);
      },
      products() {
        return this.$store.getters.getProducts;
      },
    },
    methods: {
      fetchProducts() {
        this.$store.dispatch('GET_PRODUCTS_FOR_CATEGORY', this.slug);
      },
    },
    watch: {
      $route: 'fetchProducts',
    },
  };
</script>
