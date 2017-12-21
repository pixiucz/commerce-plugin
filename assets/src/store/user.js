/* eslint-disable no-param-reassign */

import Vue from 'vue';
import { signIn, getUser, signOut } from '@/api';

export default {
  state: {
    isLoggedIn: false,
    user: {},
    addresses: [],
    orders: [],
  },
  mutations: {
    SIGN_IN(state, user) {
      state.isLoggedIn = true;
      Vue.set(state, 'user', user);
    },
    SIGN_OUT(state) {
      state.isLoggedIn = false;
      Vue.set(state, 'user', {});
      Vue.set(state, 'addresses', []);
    },
    SET_USER(state, user) {
      state.isLoggedIn = true;
      Vue.set(state, 'user', user);
    },
    SET_ADDRESSES(state, addresses) {
      Vue.set(state, 'addresses', addresses);
    },
    SET_ORDERS(state, orders) {
      Vue.set(state, 'orders', orders);
    },
  },
  actions: {
    async SIGN_IN({ commit }, credentials) {
      const response = await signIn(credentials);
      commit('SIGN_IN', response.user);
      return true;
    },
    async SIGN_OUT({ commit }) {
      await signOut();
      commit('SIGN_OUT');
      return true;
    },
    async SET_USER({ commit }) {
      const response = await getUser();
      if ('user' in response.body) {
        commit('SET_USER', response.body.user);
        commit('SET_ADDRESSES', response.body.addresses);
        commit('SET_ORDERS', response.body.orders);
      }
      return true;
    },
  },
  getters: {
    isLoggedIn: state => state.isLoggedIn,
    getUser: state => state.user,
    getOrders: state => state.orders,
  },
};
