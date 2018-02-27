<template>
  <div>
    <b-img v-if="product.primary_picture" fluid :src="product.primary_picture.path" />
    <b-img v-else fluid src="/static/img/tz_placeholder.jpg" />
  </div>
</template>

<script>
export default {
  name: 'ProductImage',
  props: ['product'],
};
</script>

<style>

</style>
