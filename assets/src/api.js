import Vue from 'vue';
import { Message } from 'element-ui';

// import store from './store/store';
import i18n from './lang/locale';

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
      resolve(result);
    }).catch((error) => {
      reject(handleReject(error));
    });
  });
}

export function getKokot() {}
