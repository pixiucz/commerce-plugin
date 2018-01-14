<template>
  <b-container class="sidebar-cart mt-3">
    <h1 class="text-center"> {{ $t('sidebar.user.title')}} <span @click="closeSidebar" class="pull-right pointer">></span></h1>
    <div class="mt-3" v-loading="isLoading">
      <div v-if="!isLoggedIn">
        <el-form :model="userForm" :rules="rules" ref="userForm">
          <el-form-item prop="email">
            <el-input placeholder="Email" v-model="userForm.email"></el-input>
          </el-form-item>
          <el-form-item prop="password">
            <el-input type="password" placeholder="Heslo" v-model="userForm.password"></el-input>
          </el-form-item>
          <el-button @click="signIn" class="full-width-btn" type="primary">{{ $t('sidebar.user.buttons.login') }}</el-button>
          <p class="text-center or-btn"> - {{ $t('sidebar.user.misc.or') }} - </p>
          <el-button @click="register" class="full-width-btn" type="primary"> {{ $t('sidebar.user.buttons.registration') }} </el-button>
        </el-form>
      </div>
      <div v-else>
        <el-button @click="signOut" class="full-width-btn" type="primary">{{ $t('sidebar.user.buttons.logout') }}</el-button>

        <div class="pt-3">
          <h4 class="text-center"> Detail </h4>
          <div class="bordered-content">
            <b-row>
              <b-col cols="3" class="text-right mt-1">
                {{ $t('sidebar.user.form.name') }}
              </b-col>
              <b-col cols="9">
                <el-input @change="userDetailTouched = true" name="name" v-model="user.name"></el-input>
              </b-col>
            </b-row>
            <b-row>
              <b-col cols="3" class="text-right mt-1">
                {{ $t('sidebar.user.form.surname') }}
              </b-col>
              <b-col cols="9">
                <el-input @blur="userDetailTouched = true" name="surname" v-model="user.surname"></el-input>
              </b-col>
            </b-row>
            <el-button v-if="userDetailTouched" @click="updateUserDetail" class="full-width-btn mt-2" type="primary">
              {{ $t('sidebar.user.buttons.saveChanges') }}
            </el-button>
          </div>
        </div>
        
        <div id="addresses" class="pt-3">
          <h4 class="text-center"> Adresy </h4>
          <div class="bordered-content">
            <el-button id="add-address-btn" type="primary" class="full-width-btn" @click="isAddingAddress = !isAddingAddress">
              <span v-if="!isAddingAddress">
                <i class="el-icon-arrow-down"></i>
                Přidat adresu
              </span>
              <span v-else>
                <i class="el-icon-arrow-up"></i>
                Skrýt
              </span>
            </el-button>
            <div v-if="isAddingAddress">
              <address-form></address-form>
            </div>
            <address-card v-for="address in addresses" :key="address.id" :address="address"></address-card>
          </div>
        </div>
      </div>

    </div>
  </b-container>
</template>

<script>
  import AddressCard from '@/components/user/AddressCard';
  import AddressForm from '@/components/user/AddressForm';

  export default {
    name: 'user',
    components: {
      'address-card': AddressCard,
      'address-form': AddressForm,
    },
    data() {
      return ({
        userForm: {
          email: 'test@test.cz',
          password: 'test',
        },
        isLoading: false,
        isAddingAddress: false,
        userDetailTouched: false,
      });
    },
    methods: {
      async signIn() {
        if (!this.isFormValid()) {
          return;
        }
        this.isLoading = true;
        await this.$store.dispatch('SIGN_IN', { login: this.userForm.email, password: this.userForm.password });
        this.isLoading = false;

        this.$message(this.$t('messages.userSignedIn'));
      },
      async signOut() {
        this.isLoading = true;
        await this.$store.dispatch('SIGN_OUT');
        this.isLoading = false;
      },
      updateUserDetail() {
        this.isLoading = true;
        setTimeout(() => {
          this.userDetailTouched = false;
          this.isLoading = false;
        }, 1000);
        this.$message('😱 😱 😱 😱 Ještě není implementováno 😱 😱 😱 😱 😱');
      },
      async register() {
        if (!this.isFormValid()) {
          return;
        }

        this.isLoading = true;
        await this.$store.dispatch('REGISTER', { login: this.userForm.email, password: this.userForm.password });
        this.isLoading = false;

        this.$message(this.$t('messages.userSignedIn'));
      },
      isFormValid() {
        let result = false;
        this.$refs.userForm.validate((value) => {
          result = value;
        });
        return result;
      },
    },
    computed: {
      isLoggedIn() {
        return this.$store.getters.isLoggedIn;
      },
      user() {
        return this.$store.getters.getUser;
      },
      rules() {
        return {
          email: [
            {
              required: true, message: this.$t('sidebar.user.form.emailRequired'), trigger: 'blur',
            },
            {
              type: 'email', message: this.$t('sidebar.user.form.emailTypeEmail'), trigger: 'blur',
            },
          ],
          password: [
            {
              required: true, message: this.$t('sidebar.user.form.passwordRequired'), trigger: 'blur',
            },
            {
              min: 4, message: this.$t('sidebar.user.form.passwordMin'), trigger: 'blur',
            },
          ],
        };
      },
      addresses() {
        return this.$store.getters.getAddresses;
      },
    },
  };
</script>

<style scoped>
  .or-btn {
    margin-top: 15px;
  }

  #detail {
    padding: 10px 10px 10px 10px;
    border: black 1px solid;
  } 

  .bordered-content {
    padding: 10px 10px 10px 10px;
    border: black 1px solid;
  }

  #add-address-btn {
    margin-bottom: 20px;
  }
</style>
