import Vue from 'vue';

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

export function getProducts(categorySlug) {
  return new Promise((resolve, reject) => {
    Vue.http.get(`${API}/category/${categorySlug}/products`).then((result) => {
      resolve(result.body.products);
    }).catch((error) => {
      reject(error);
    });
  });
}
