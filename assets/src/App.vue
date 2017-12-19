<template>
  <div id="app">
    <div class="page" :class="{'page--shifted': $store.state.sidebar.show}" 
      @click="$store.commit('SET_SIDEBAR_VISIBLE', false)">
      <b-navbar toggleable="md" type="dark" variant="dark">
        <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>

        <b-container>
          <b-navbar-brand>Turistické známky</b-navbar-brand>

          <b-collapse is-nav id="nav_collapse">
            <b-navbar-nav class="ml-auto">
              <b-nav-item>Staré VTZ</b-nav-item>
              <b-nav-item-dropdown text="Doplnky" right>
                <b-dropdown-item v-for="category in categories" :key="category.id" href="#">
                <router-link :to="{ name: 'category', params: { slug: category.slug }}">
                  {{ category.name }}
                </router-link>
              </b-dropdown-item>
              </b-nav-item-dropdown>
              <b-nav-item @click.stop="toggleSidebar('Orders')">
                <i class="fa fa-list-alt" />
              </b-nav-item>
              <b-nav-item @click.stop="toggleSidebar('User')">
                <i class="fa fa-user-circle" />
              </b-nav-item>
              <b-nav-item @click.stop="toggleSidebar('Cart')">
                <i class="fa fa-shopping-cart">
                  <el-badge class="cart-badge" v-if="itemsInCart > 0" :value="itemsInCart" />
                </i>
              </b-nav-item>
              <b-nav-item>
                <el-button round size="small">Späť na web</el-button>
              </b-nav-item>
            </b-navbar-nav>
          </b-collapse>
        </b-container>
      </b-navbar>

      <b-container>
        <router-view/>
      </b-container>
    </div>

    <sidebar />
  </div>
</template>

<script>
  import Sidebar from '@/components/sidebar/SideBar';
  import { SidebarRoutes } from '@/store/store';

  export default {
    name: 'app',
    watch: {
      $route(to) {
        if (SidebarRoutes.includes(to.name)) {
          this.$store.commit('SET_SIDEBAR_ROUTE', to.name);
        }
      },
    },
    methods: {
      toggleSidebar(route) {
        const show = !(this.$store.state.sidebar.show && route === this.$store.state.sidebar.route);
        this.$store.commit('SET_SIDEBAR_VISIBLE', show);
        this.$store.commit('SET_SIDEBAR_ROUTE', route);
      },
    },
    async created() {
      await this.$store.dispatch('GET_CATEGORIES');
    },
    components: {
      Sidebar,
    },
    computed: {
      categories() {
        return this.$store.state.products.categories;
      },
      itemsInCart() {
        return this.$store.getters.getCartLength;
      },
    },
  };
</script>

<style lang="scss" scoped>
  @import 'assets/style.scss';

  .page {
    transition: $sidebar-transition;

    &--shifted {
      transform: translateX(-$sidebar-width);
    }
  }

  .cart-badge {
    background-color: #353a40;
  }

  .el-badge__content {
    background-color: #353a40;
  }
</style>
