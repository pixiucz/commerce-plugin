/* eslint-disable no-param-reassign */

import Vue from 'vue';
import Vuex from 'vuex';

import user from './user';
import cart from './cart';
import products from './products';

Vue.use(Vuex);

export const SidebarRoutes = [
  'cart',
  'orders',
  'order',
  'user',
];

export default new Vuex.Store({
  state: {
    sidebarRoute: SidebarRoutes[0],
  },

  mutations: {
    SET_SIDEBAR_ROUTE(state, route) {
      if (SidebarRoutes.indexOf(route) === -1) {
        throw new Error('Unknown sidebar route!');
      }
      state.sidebarRoute = route;
    },
  },
  modules: {
    user,
    cart,
    products,
  },
});
