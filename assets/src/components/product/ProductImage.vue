<template>
  <div>
    <b-img v-if="product.primary_picture" fluid :src="product.primary_picture.path" />
    <b-img v-else fluid src="http://tz.pixiu.cz/storage/app/uploads/public/595/bab/3d2/595bab3d23cec055399992.png" />
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
