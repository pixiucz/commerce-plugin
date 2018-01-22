/* eslint-disable no-param-reassign */

import { extractOrderItemsMetaData } from '@/helpers';
import { storeOrder } from '@/api';

export default {
  state: {
    address: '',
    deliveryOption: '',
    paymentMethod: '',
  },
  mutations: {
    SET_ADDRESS(state, address) {
      state.address = address;
    },
    SET_DELIVERY_OPTION(state, optionId) {
      state.deliveryOption = optionId;
    },
    SET_PAYMENT_METHOD(state, paymentId) {
      state.paymentMethod = paymentId;
    },
  },
  getters: {
    getSubmitedAddress: state => state.address,
    getSubmitedDeliveryOption: state => state.deliveryOption,
    getSubmitedPaymentMethod: state => state.paymentMethod,
  },
  actions: {
    SUBMIT_ORDER({ getters }, { address, deliveryOption, paymentMethod }) {
      const orderItems = extractOrderItemsMetaData(getters.getCartItems);

      storeOrder({
        orderItems,
        delivery_address: address,
        deliveryOption,
        paymentMethod,
      });
    },
  },
};
