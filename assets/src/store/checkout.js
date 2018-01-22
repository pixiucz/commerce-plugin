/* eslint-disable no-param-reassign */

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
};
