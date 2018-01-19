<template>
  <el-form :model="address" :rules="rules" ref="addressForm">
    <div class="row">
      <div class="col-md-6">
      <el-form-item prop="first_name">
        <el-input name="first_name" v-model="address.first_name" :placeholder="$t('sidebar.user.form.name')"></el-input>
      </el-form-item>
      </div>
      <div class="col-md-6">
      <el-form-item prop="last_name">
        <el-input name="last_name" v-model="address.last_name" :placeholder="$t('sidebar.user.form.surname')"></el-input>
      </el-form-item>
      </div>
    </div>
    <el-form-item prop="telephone">
      <el-input name="telephone" v-model="address.telephone" :placeholder="$t('sidebar.user.form.phone')"></el-input>
    </el-form-item>
    <el-form-item prop="address">
      <el-input name="address" v-model="address.address" :placeholder="$t('sidebar.user.form.address')"></el-input>
    </el-form-item>
    <div class="row">
      <div class="col-md-8">
        <el-form-item prop="city">
          <el-input name="city" v-model="address.city" :placeholder="$t('sidebar.user.form.city')"></el-input>
        </el-form-item>
      </div>
      <div class="col-md-4">
        <el-form-item prop="zip">
          <el-input name="zip" v-model="address.zip" :placeholder="$t('sidebar.user.form.zip')"></el-input>
        </el-form-item>
      </div>
    </div>
    <el-form-item prop="country">
      <el-select v-model="address.country" :placeholder="$t('sidebar.user.form.country')">
        <el-option
          value="Slovakia"
          label="Slovenská republika"
        >
        </el-option>
        <el-option
          value="Czech Republic"
          label="Česká republika"
        >
        </el-option>
      </el-select>
    </el-form-item>
  </el-form>
</template>

<script>
export default {
  name: 'AddressForm',
  props: ['address'],
  data() {
    return ({
      rules: {
        first_name: [
          { required: true, message: this.$t('other.fieldRequired'), trigger: 'blur' },
        ],
        last_name: [
          { required: true, message: this.$t('other.fieldRequired'), trigger: 'blur' },
        ],
        telephone: [
          { validator: this.checkPhone },
        ],
        address: [
          { required: true, message: this.$t('other.fieldRequired'), trigger: 'blur' },
        ],
        city: [
          { required: true, message: this.$t('other.fieldRequired'), trigger: 'blur' },
        ],
        zip: [
          { validator: this.checkPostal },
        ],
        country: [
          { required: true, message: this.$t('other.fieldRequired'), trigger: 'blur' },
        ],
      },
      isLoading: false,
    });
  },
  methods: {
    checkPhone(rule, value, callback) {
      if (!value) {
        return callback(new Error(this.$t('sidebar.user.form.phoneRequired')));
      }

      if (!value.match(/^\+[0-9]+/)) {
        return callback(new Error(this.$t('sidebar.user.form.countryCode')));
      }

      return callback();
    },
    checkPostal(rules, value, callback) {
      if (!value) {
        return callback(new Error(this.$t('sidebar.user.form.postalCodeRequired')));
      }

      if (!value.match(/^[0-9]{3} [0-9]{2}$/)) {
        return callback(new Error(this.$t('sidebar.user.form.correctPostalCode')));
      }

      return callback();
    },
    isFormValid() {
      let result = false;
      this.$refs.addressForm.validate((value) => {
        result = value;
      });
      return result;
    },
  },
};
</script>

<style scoped>

</style>
