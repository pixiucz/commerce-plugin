import Vue from 'vue';
import { transformObjectToQueryParams } from './helpers';

const API = '/api/v1';

export function getCategories() {
  return new Promise((resolve, reject) => {
    Vue.http.get(`${API}/category/`).then((result) => {
      resolve(result.body.categories);
    }).catch((error) => {
      reject(error);
    });
  });
}

export function getProductsForCategory({ categorySlug, paginator }) {
  return new Promise((resolve, reject) => {
    Vue.http.get(`${API}/category/${categorySlug}/products/?${transformObjectToQueryParams(paginator)}`).then((result) => {
      resolve(result.body);
    }).catch((error) => {
      reject(error);
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
