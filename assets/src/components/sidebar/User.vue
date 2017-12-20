<template>
  <b-container class="sidebar-cart mt-3">
    <h1 class="text-center"> Uživatel <span @click="closeSidebar" class="pull-right pointer">></span></h1>
    <div class="" v-loading="isLoading">
      <div v-if="!isLoggedIn">
        <el-input name="email" placeholder="Email" v-model="email"></el-input>
        <el-input name="password" type="password" placeholder="Heslo" v-model="password"></el-input>
        <el-button @click="signIn" class="sign-in-btn" type="primary">Přihlásit</el-button>
      </div>
      <div v-else>
        <el-button @click="signOut" class="sign-in-btn" type="primary">Odhlásit</el-button>
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
