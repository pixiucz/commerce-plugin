<template>
  <div class="text-center filters-toolbar">

    <el-radio-group v-model="orderDir" size="small">
      <el-radio-button label="desc">
        <i class="el-icon-arrow-down"> </i>
        Sestupně
      </el-radio-button>
      <el-radio-button label="asc">
        <i class="el-icon-arrow-up"> </i>
        Vzestupně
      </el-radio-button>
    </el-radio-group>
  </div>
</template>

<script>
export default {
  data() {
    return ({
      orderDir: 'desc',
      orderBy: '',
    });
  },
  watch: {
    orderDir(val) {
      this.$router.push({ query: Object.assign({}, this.$route.query, { orderDir: val }) });
    },
  },
};
</script>

<style>
.filters-toolbar {
  margin-bottom: 45px;
}
</style>
