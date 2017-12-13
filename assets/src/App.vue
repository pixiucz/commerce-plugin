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
  export default {
    name: 'app',
  };
</script>

<style>

</style>
