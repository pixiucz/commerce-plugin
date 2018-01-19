<template>
  <div>
    <el-card :class="classProp">
      <button type="button" class="close" aria-label="Close" @click="deleteAddress">
        <i class="el-icon-close"></i>
      </button>
      <div class="text item text-center">
        {{ address.first_name }} {{ address.last_name }} <br>
        Tel: {{ address.telephone }} <br>
        {{ address.address }} <br>
        {{ address.city }}, {{ address.zip }} <br> 
      </div>
    </el-card>
  </div>
</template>

<script>
export default {
  name: 'AddressCard',
  props: ['address', 'classProp'],
  methods: {
    deleteAddress() {
      this.$confirm(this.$t('messages.deleteAddressConfirm'), '', {
        confirmButtonText: this.$t('other.confirm'),
        cancelButtonText: this.$t('other.cancel'),
        type: 'warning',
      }).then(() => {
        this.$store.dispatch('DELETE_ADDRESS', this.address.id);
        this.$message({
          type: 'success',
          message: this.$t('messages.deleteCompleted'),
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: this.$t('messages.deleteCanceled'),
        });
      });
    },
  },
};
</script>

<style>
  .close:hover {
    cursor: pointer;
  }
</style>
