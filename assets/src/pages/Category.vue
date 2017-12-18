<template>
  <b-container v-if="category" class="mt-3 mb-3">
    <b-row>
      <b-col cols="6">
        <h1>{{ category.name }}</h1>
      </b-col>
      <b-col cols="6 float-right">
        <product-filter :filters="filters"></product-filter>
      </b-col>
    </b-row>
    <catalogue-grid :products="products" v-loading="isLoading"></catalogue-grid>
    <div class="text-center">
      <el-pagination
        background
        layout="prev, pager, next"
        :current-page.sync="currentPage"
        :page-size="this.params.limit"
        :total="productsCount">
      </el-pagination>
    </div>
  </b-container>
</template>

<script>
  import CatalogueGrid from '@/components/catalogue/CatalogueGrid';
  import ProductFilter from '@/components/catalogue/ProductFilter';

  export default {
    name: 'category',
    props: ['slug'],
    components: {
      'catalogue-grid': CatalogueGrid,
      'product-filter': ProductFilter,
    },
    data() {
      return {
        params: {
          limit: 12,
          offset: 0,
          orderBy: 'id',
          orderDir: 'desc',
        },
        isLoading: true,
        filters: [
          {
            label: 'podle názvu zostupne',
            value: 'slug-desc',
          },
          {
            label: 'podle názvu vzostupne',
            value: 'slug-asc',
          },
          {
            label: 'podle ceny zostupne',
            value: 'price-desc',
          },
          {
            label: 'podle ceny vzostupne',
            value: 'price-asc',
          },
        ],
      };
    },
    async mounted() {
      this.checkRouteParams();
      this.fetchProducts();
    },
    computed: {
      category() {
        return this.$store.getters.getCategory(this.slug);
      },
      products() {
        return this.$store.getters.getProducts;
      },
      productsCount() {
        return this.$store.getters.getProductsCount;
      },
      currentPage: {
        get() {
          return (this.params.offset % this.params.limit) + 1;
        },
        set(val) {
          this.params.offset = this.params.limit * (val - 1);
          this.$router.push({
            query: Object.assign({}, this.$route.query, {
              limit: this.params.limit,
              offset: this.params.offset,
            }),
          });
        },
      },
    },
    methods: {
      async fetchProducts() {
        this.isLoading = true;

        await this.$store.dispatch('GET_PRODUCTS_FOR_CATEGORY', {
          categorySlug: this.slug,
          paginator: {
            limit: this.params.limit,
            offset: this.params.offset,
            orderBy: this.params.orderBy,
            orderDir: this.params.orderDir,
          },
        });

        this.isLoading = false;
      },
      stopLoading() {
        this.isLoading = false;
      },
      handleRouteChange() {
        this.checkRouteParams();
        this.fetchProducts();
      },
      checkRouteParams() {
        if ('limit' in this.$route.query) {
          this.params.limit = parseInt(this.$route.query.limit, 10);
        }

        if ('offset' in this.$route.query) {
          this.params.offset = parseInt(this.$route.query.offset, 10);
        }

        if ('orderDir' in this.$route.query) {
          this.params.orderDir = this.$route.query.orderDir;
        }

        if ('orderBy' in this.$route.query) {
          this.params.orderBy = this.$route.query.orderBy;
        }
      },
    },
    watch: {
      $route: 'handleRouteChange',
    },
  };
</script>
