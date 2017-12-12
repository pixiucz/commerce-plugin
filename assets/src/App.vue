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
  import ElementUI from 'element-ui';
  import '../lib/element-theme/index.css';

  Vue.use(ElementUI);

  export default {
    name: 'app',
  };
</script>

<style>
  #app {
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    margin-top: 60px;
  }
</style>
