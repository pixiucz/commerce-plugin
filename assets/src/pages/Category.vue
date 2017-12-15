<template>
  <b-container v-if="category" class="mt-3">
    <h1 class="text-center to-upper"> {{ category.name }} </h1>
    <catalogue-grid :products="products"></catalogue-grid>
  </b-container>
</template>

<script>
  import CatalogueGrid from '../components/catalogue/CatalogueGrid';

  export default {
    name: 'category',
    props: ['slug'],
    components: {
      'catalogue-grid': CatalogueGrid,
    },
    data() {
      return {
        params: {
          limit: 25,
          offset: 0,
        },
      };
    },
    async mounted() {
      await this.fetchProducts();
      if ('limit' in this.$route.query) {
        this.params.limit = this.$route.query.limit;
      }

      if ('offset' in this.$route.query) {
        this.params.offset = this.$route.query.offset;
      }
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
