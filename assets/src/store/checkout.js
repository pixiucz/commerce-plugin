/* eslint-disable no-param-reassign */

import { extractOrderItemsMetaData } from '@/helpers';
import { storeOrder } from '@/api';

export default {
  state: {
    address: '',
    billingAddress: '',
    deliveryOption: '',
    paymentMethod: '',
  },
  mutations: {
    SET_ADDRESS(state, address) {
      state.address = address;
    },
    SET_BILLING_ADDRESS(state, billingAddress) {
      state.billingAddress = billingAddress;
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
    getSubmitedBillingAddress: state => state.billingAddress,
    getSubmitedDeliveryOption: state => state.deliveryOption,
    getSubmitedPaymentMethod: state => state.paymentMethod,
  },
  actions: {
    async SUBMIT_ORDER({ getters }, { address, deliveryOption, paymentMethod, billingAddress }) {
      const orderItems = extractOrderItemsMetaData(getters.getCartItems);

      const order = {
        orderItems,
        delivery_address: address,
        deliveryOption,
        paymentMethod,
      };

      if (billingAddress) {
        order.billingAddress = billingAddress;
      }

      await storeOrder(order);

      return true;
    },
  },
};
