<template>
  <div id="app">
    <b-navbar toggleable="md" type="dark" variant="dark">
      <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>

      <b-container>
        <b-navbar-brand>Turistické známky</b-navbar-brand>

        <b-collapse is-nav id="nav_collapse">
          <b-navbar-nav class="ml-auto">
            <b-nav-item>Staré VTZ</b-nav-item>
            <b-nav-item-dropdown text="Doplnky" right>
              <b-dropdown-item href="#">Výročné známky</b-dropdown-item>
              <b-dropdown-item href="#">Zrušené známky</b-dropdown-item>
            </b-nav-item-dropdown>
            <b-nav-item>
              <i class="fa fa-list-alt" />
            </b-nav-item>
            <b-nav-item>
              <i class="fa fa-user-circle" />
            </b-nav-item>
            <b-nav-item>
              <i class="fa fa-shopping-cart" />
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
</template>

<script>
  import { SidebarRoutes } from './store/store';

  export default {
    name: 'app',
    watch: {
      $route(to) {
        if (SidebarRoutes.includes(to.name)) {
          this.$store.commit('SET_SIDEBAR_ROUTE', to.name);
        }
      },
    },
    async created() {
      console.log(await this.$store.dispatch('GET_CATEGORIES'));
    },
  };
</script>
