/* eslint-disable no-param-reassign */

import Vue from 'vue';

import { getCategories, getProductsForCategory, getProducts } from '../api';

export default {
  state: {
    categories: [],
    products: [],
    productsCount: 0,
  },
  mutations: {
    SET_CATEGORIES(state, categories) {
      Vue.set(state, 'categories', categories);
    },
    SET_PRODUCTS(state, { products, totalCount }) {
      Vue.set(state, 'products', products);
      state.productsCount = totalCount;
    },
  },
  actions: {
    async GET_CATEGORIES({ commit, state }) {
      if (!state.categories.length) {
        commit('SET_CATEGORIES', await getCategories());
      }
      return state.categories;
    },
    async GET_PRODUCTS_FOR_CATEGORY({ commit, state }, request) {
      commit('SET_PRODUCTS', await getProductsForCategory(request));
    },
    async GET_PRODUCTS({ commit }, paginator = { limit: 10 }) {
      const response = await getProducts(paginator);
      response.totalCount = 10; // FIXME
      commit('SET_PRODUCTS', response);
    },
  },
  getters: {
    getCategory: state => slug => state.categories.find(category => category.slug === slug),
    getProducts: state => state.products,
    getProduct: state => slug => state.products.find(product => product.slug === slug),
    getProductsCount: state => state.productsCount,
  },
};
