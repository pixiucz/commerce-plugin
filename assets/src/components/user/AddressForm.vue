<template>
  <el-form :model="newAddressForm" :rules="rules">
    <div class="row">
      <div class="col-md-6">
      <el-form-item prop="first_name">
        <el-input v-model="newAddressForm.first_name" placeholder="Jméno"></el-input>
      </el-form-item>
      </div>
      <div class="col-md-6">
      <el-form-item prop="last_name">
        <el-input v-model="newAddressForm.last_name" placeholder="Příjmení"></el-input>
      </el-form-item>
      </div>
    </div>
    <el-form-item prop="telephone">
      <el-input v-model="newAddressForm.telephone" placeholder="Telefonní číslo"></el-input>
    </el-form-item>
    <el-form-item prop="address">
      <el-input v-model="newAddressForm.address" placeholder="Adresa"></el-input>
    </el-form-item>
    <div class="row">
      <div class="col-md-8">
        <el-form-item prop="city">
          <el-input v-model="newAddressForm.city" placeholder="Město"></el-input>
        </el-form-item>
      </div>
      <div class="col-md-4">
        <el-form-item prop="zip">
          <el-input v-model="newAddressForm.zip" placeholder="PSČ"></el-input>
        </el-form-item>
      </div>
    </div>
    <el-form-item prop="country">
      <el-select v-model="newAddressForm.country" placeholder="Země">
        <el-option
          value="Slovakia"
          label="Slovenska republika"
        >
        </el-option>
        <el-option
          value="Czech Republic"
          label="Česká republika"
        >
        </el-option>
      </el-select>
    </el-form-item>
    <el-button :loading="isLoading" @click="addAddress" type="primary" class="full-width-btn">Uložiť</el-button>
  </el-form>
</template>

<script>
export default {
  name: 'AddressForm',
  data() {
    return ({
      newAddressForm: {
        address: '',
        city: '',
        company: '',
        country: '',
        dic: '',
        first_name: '',
        last_name: '',
        telephone: '',
        zip: '',
      },
      rules: {
        first_name: [
          { required: true, message: 'Je potřeba vyplnit', trigger: 'blur' },
        ],
        last_name: [
          { required: true, message: 'Je potřeba vyplnit', trigger: 'blur' },
        ],
        telephone: [
          { required: true, message: 'Je potřeba vyplnit', trigger: 'blur' },
        ],
        address: [
          { required: true, message: 'Je potřeba vyplnit', trigger: 'blur' },
        ],
        city: [
          { required: true, message: 'Je potřeba vyplnit', trigger: 'blur' },
        ],
        zip: [
          { required: true, message: 'Je potřeba vyplnit', trigger: 'blur' },
        ],
        country: [
          { required: true, message: 'Je potřeba vyplnit', trigger: 'blur' },
        ],
      },
      isLoading: false,
    });
  },
  methods: {
    async addAddress() {
      this.isLoading = true;
      await this.$store.dispatch('ADD_ADDRESS', this.newAddressForm);
      this.isLoading = false;
      this.resetForm();
      this.$emit('added');
    },
    resetForm() {
      this.newAddressForm = {
        address: '',
        city: '',
        company: '',
        country: '',
        dic: '',
        first_name: '',
        last_name: '',
        telephone: '',
        zip: '',
      };
    },
  },
};
</script>

<style scoped>

</style>
