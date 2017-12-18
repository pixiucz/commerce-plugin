import Vue from 'vue';
import { Message } from 'element-ui';

// import store from './store/store';
import i18n from './lang/locale';
import { transformObjectToQueryParams } from './helpers';

const API = '/api/v1';

/**
 * Function for handling of all rejected api calls.
 */
function handleReject(error, message = true) {
  if (message) {
    Message({
      type: 'error',
      showClose: true,
      message: (process.env.NODE_ENV === 'production')
        ? i18n.t('error.api', { status: error.status })
        : error,
    });
  }

  switch (error.status) {
    case 404: // wrong url
      break;
    case 401: // authorization
      // store.dispatch('LOGOUT');
      break;
    default:
      break;
  }

  return error;
}

export function getCategories() {
  return new Promise((resolve, reject) => {
    Vue.http.get(`${API}/category/`).then((result) => {
      resolve(result.body.categories);
    }).catch((error) => {
      reject(handleReject(error));
    });
  });
}

export function getProductsForCategory({ categorySlug, paginator }) {
  return new Promise((resolve, reject) => {
    Vue.http.get(`${API}/category/${categorySlug}/products/?${transformObjectToQueryParams(paginator)}`).then((result) => {
      resolve(result.body);
    }).catch((error) => {
      reject(handleReject(error));
    });
  });
}

export function getProduct(productSlug) {
  return new Promise((resolve, reject) => {
    Vue.http.get(`${API}/product/${productSlug}/`).then((result) => {
      resolve(result.body.product);
    }).catch((error) => {
      reject(error);
    });
  });
}
