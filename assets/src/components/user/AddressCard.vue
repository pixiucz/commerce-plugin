<template>
  <div>
    <el-card class="box-card mt-3">
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
  props: ['address'],
  methods: {
    deleteAddress() {
      this.$confirm(this.$t('messages.deleteAddressConfirm'), 'Warning', {
        confirmButtonText: this.$t('other.confirm'),
        cancelButtonText: this.$t('other.cancel'),
        type: 'warning',
      }).then(() => {
        this.$store.dispatch('DELETE_ADDRESS', this.address.id);
        this.$message({
          type: 'success',
          message: 'Delete completed',
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
    },
  },
};
</script>

<style>
  .box-card {
    background-color: #25282d;
    border: none;
    color: white;
  }

  .close:hover {
    cursor: pointer;
  }
</style>
