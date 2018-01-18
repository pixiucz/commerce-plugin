<template>
  <b-container>
    <h1> Dokončení objednávky </h1>
    <div style="height: 200px;">
    </div>
  </b-container>
</template>

<script>
export default {
  computed: {
    items() {
      return this.$store.getters.getCartItems;
    },
  },
};
</script>

<style>

</style>
