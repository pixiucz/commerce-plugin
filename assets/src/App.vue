<template>
  <div id="app">
    <el-input />
    <el-button type="primary">Kokoti</el-button>
    <el-select value="fef">
      <el-option label="Jijh" value="fef" />
      <el-option label="Jijfgh" value="fefef" />
      <el-option label="Jigjh" value="wufi" />
    </el-select>
    
    <router-view/>
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
  };
</script>
