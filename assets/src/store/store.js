/* eslint-disable no-param-reassign */

import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate';

import user from './user';
import cart from './cart';
import products from './products';
import checkout from './checkout';

Vue.use(Vuex);

export const SidebarRoutes = [
  'Cart',
  'Orders',
  'Order',
  'User',
];

export default new Vuex.Store({
  state: {
    sidebar: {
      route: SidebarRoutes[0],
      show: false,
    },
  },

  mutations: {
    SET_SIDEBAR_ROUTE(state, route) {
      if (SidebarRoutes.indexOf(route) === -1) {
        throw new Error('Unknown sidebar route!');
      }
      state.sidebar.route = route;
    },
    SET_SIDEBAR_VISIBLE(state, visibility) {
      state.sidebar.show = visibility;
    },
  },
  modules: {
    user,
    cart,
    products,
    checkout,
  },
  plugins: [
    createPersistedState({
      paths: ['cart', 'checkout'],
    }),
  ],
});
