import Vue from 'vue';

export default {
  state: {
    items: [],
  },
  actions: {
    ADD_TO_CART({ commit, getters, state }, item) {
      const existingItemIndex = getters.getItemIndexFromCart(item.product.slug);

      if (existingItemIndex !== -1) {
        const existingItem = state.items[existingItemIndex];
        existingItem.amount += item.amount;
        commit('UPDATE_ITEM', {
          index: existingItemIndex,
          updatedItem: existingItem,
        });
        return;
      }

      commit('ADD_TO_CART', item);
    },
  },
  mutations: {
    ADD_TO_CART(state, item) {
      state.items.push(item);
    },
    UPDATE_ITEM(state, { index, updatedItem }) {
      Vue.set(state.items, index, updatedItem);
    },
  },
  getters: {
    getItemFromCart: state => slug => state.items.find(item => item.product.slug === slug),
    getItemIndexFromCart: state =>
      slug => state.items.findIndex(
        item => item.product.slug === slug),
    getCartLength: state => state.items.length,
  },
};
