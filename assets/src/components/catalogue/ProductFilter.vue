<template>
  <div class="pull-right mt-3">
    Zoradiť podľa: 
    <el-select v-model="orderBy" placeholder="vybrat">
      <el-option v-for="filter in filters" :value="filter.value" :label="filter.label" :key="filter.value">
      </el-option>
    </el-select>
  </div>
</template>

<script>
export default {
  props: ['filters'],
  data() {
    return ({
      activeNames: [],
      orderBy: '',
    });
  },
  watch: {
    orderBy(val) {
      const args = val.split('-');
      
      this.$router.push({ query: Object.assign({}, this.$route.query, { orderBy: args[0], orderDir: args[1] }) });
    },
  },
};
</script>

<style>
.filters-toolbar {
  margin-bottom: 45px;
}
</style>
