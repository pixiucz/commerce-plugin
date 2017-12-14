/* eslint-disable no-param-reassign */

import Vue from 'vue';

import { getCategories, getProducts } from '../api';

export default {
  state: {
    categories: [],
    products: [],
  },
  mutations: {
    SET_CATEGORIES(state, categories) {
      Vue.set(state, 'categories', categories);
    },
    SET_PRODUCTS(state, products) {
      Vue.set(state, 'products', products);
    },
  },
  actions: {
    async GET_CATEGORIES({ commit, state }) {
      if (!state.categories.length) {
        commit('SET_CATEGORIES', await getCategories());
      }
      return state.categories;
    },
    async GET_PRODUCTS_FOR_CATEGORY({ commit, state }, categorySlug) {
      commit('SET_PRODUCTS', await getProducts(categorySlug));
    },
  },
  getters: {
    getCategory: state => slug => state.categories.find(category => category.slug === slug),
    getProducts: state => state.products,
    getProduct: state => slug => state.products.find(product => product.slug === slug),
  },
};
