import Vue from 'vue';

const API = '/api/v1';

export function getCategories() {
  return new Promise((resolve, reject) => {
    Vue.http.get(`${API}/category/`).then((result) => {
      resolve(result.body);
    }).catch((error) => {
      reject(error);
    });
  });
}

export function getKokot() {}
