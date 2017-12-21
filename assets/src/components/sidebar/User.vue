<template>
  <b-container class="sidebar-cart mt-3">
    <h1 class="text-center"> {{ $t('sidebar.user.title')}} <span @click="closeSidebar" class="pull-right pointer">></span></h1>
    <div class="mt-3" v-loading="isLoading">
      <div v-if="!isLoggedIn">
        <el-input name="email" placeholder="Email" v-model="email"></el-input>
        <el-input name="password" type="password" placeholder="Heslo" v-model="password"></el-input>
        <el-button @click="signIn" class="sign-in-btn" type="primary">{{ $t('sidebar.user.buttons.login') }}</el-button>
      </div>
      <div v-else>
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
        <el-button v-if="userDetailTouched" @click="updateUserDetail" class="sign-in-btn mt-2" type="primary">
          {{ $t('sidebar.user.buttons.saveChanges') }}
        </el-button>
        <hr>
        <el-button @click="signOut" class="sign-in-btn" type="primary">{{ $t('sidebar.user.buttons.logout') }}</el-button>
      </div>
    </div>
  </b-container>
</template>

<script>
  export default {
    name: 'user',
    data() {
      return ({
        email: 'test@test.cz',
        password: 'test',
        isLoading: false,
        userDetailTouched: false,
      });
    },
    methods: {
      async signIn() {
        this.isLoading = true;
        await this.$store.dispatch('SIGN_IN', { login: this.email, password: this.password });
        this.isLoading = false;
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
    },
    computed: {
      isLoggedIn() {
        return this.$store.getters.isLoggedIn;
      },
      user() {
        return this.$store.getters.getUser;
      },
    },
  };
</script>

<style scoped>
  .sign-in-btn {
    width: 100%;
  }
</style>
