<template>
    <div class="bg-color-white hover-shadow11 padding-bottom">
        <h3 class="font-size-23 caps font-weight5 margin-bottom1 margin-top2">
            {{ product.product_name }}
        </h3>
        <p class="padding-bottom1 margin-bottom text-1"> {{ product.price }} € <i>s DPH</i></p>
        <p class="padding-bottom1 text-2">{{ product.price - (product.price * (product.tax_rate/100)) }} € <i>bez DPH</i></p>
    </div>
</template>

<script>
export default {
  props: ['product'],
};
</script>

<style>

</style>
