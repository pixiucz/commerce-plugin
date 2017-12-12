<template>
  <div id="app">

    <el-input />
    <el-button type="primary">Kokoti</el-button>
    <el-select value="fef">
      <el-option label="Jijh" value="fef" />
      <el-option label="Jijfgh" value="fefef" />
      <el-option label="Jigjh" value="wufi" />
    </el-select>
    

    <img src="./assets/logo.png">
    <router-view/>
  </div>
</template>

<script>
  import Vue from 'vue';
  import BootstrapVue from 'bootstrap-vue';
  import ElementUI from 'element-ui';

  import 'bootstrap/dist/css/bootstrap.css';
  import 'bootstrap-vue/dist/bootstrap-vue.css';
  import '../lib/element-theme/index.css';
  import '../lib/style.scss';
  
  Vue.use(ElementUI);
  Vue.use(BootstrapVue);

  export default {
    name: 'app',
  };
</script>

<style>

</style>
