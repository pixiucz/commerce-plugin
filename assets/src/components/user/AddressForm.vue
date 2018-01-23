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
    <div v-if="isBilling">
      <hr>
      <div class="row">
        <div class="col-md-6">
          <el-form-item prop="company">
            <el-input name="company" v-model="address.company" :placeholder="$t('sidebar.user.form.company')"></el-input>
          </el-form-item>
        </div>
        <div class="col-md-3">
          <el-form-item prop="ico">
            <el-input name="ico" v-model="address.ico" :placeholder="$t('sidebar.user.form.ico')"></el-input>
          </el-form-item>
        </div>
        <div class="col-md-3">
          <el-form-item prop="dic">
            <el-input name="dic" v-model="address.dic" :placeholder="$t('sidebar.user.form.dic')"></el-input>
          </el-form-item>
        </div>
      </div>
    </div>
  </el-form>
</template>

<script>
export default {
  name: 'AddressForm',
  props: {
    address: Object,
    isBilling: {
      type: Boolean,
      default: false,
    },
  },
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
        company: [
          { validator: this.checkCompany },
        ],
        ico: [
          { validator: this.checkIco },
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
    checkCompany(rules, value, callback) {
      if (this.isBilling && value === '') {
        return callback(new Error('U fakturační adresy je potřeba vyplnit jméno firmy'));
      }

      return callback();
    },
    checkIco(rules, value, callback) {
      if (this.isBilling && (value === undefined || value.length < 8 || !/^\d+$/.test(value))) {
        return callback(new Error('IČO musí být složeno z 8 číslic'));
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
