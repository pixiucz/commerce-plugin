/* eslint-disable no-param-reassign */

import Vue from 'vue';

import { getCategories } from '../api';

const mutations = {
  SET_CATEGORIES(state, categories) {
    Vue.set(state, 'categories', categories);
  },
};

const actions = {
  async GET_CATEGORIES({ commit, state }) {
    if (!state.categories.length) {
      commit('SET_CATEGORIES', await getCategories());
    }
    return state.categories;
  },
};

const state = {
  categories: [],
};

const getters = {};

export default {
  state,
  mutations,
  actions,
  getters,
};
