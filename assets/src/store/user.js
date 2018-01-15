/* eslint-disable no-param-reassign */

import Vue from 'vue';
import { signIn, getUser, signOut, register, addAddress } from '@/api';

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
      Vue.set(state, 'orders', []);
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
    ADD_ADDRESS(state, address) {
      state.addresses.push(address);
    },
  },
  actions: {
    async SIGN_IN({ commit }, credentials) {
      const response = await signIn(credentials);
      commit('SET_USER', response.body.user);
      commit('SET_ADDRESSES', response.body.addresses);
      commit('SET_ORDERS', response.body.orders);
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
    async REGISTER({ dispatch }, credentials) {
      const response = await register(credentials);
      if (response.status === 201) {
        dispatch('SIGN_IN', credentials);
      }
    },
    async ADD_ADDRESS({ commit }, address) {
      const response = await addAddress(address);
      if (response.status === 201) {
        commit('ADD_ADDRESS', response.body.address);
      }
    },
  },
  getters: {
    isLoggedIn: state => state.isLoggedIn,
    getUser: state => state.user,
    getOrders: state => state.orders,
    getAddresses: state => state.addresses,
  },
};
