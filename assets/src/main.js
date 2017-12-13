import Vue from 'vue';

import BootstrapVue from 'bootstrap-vue';
import ElementUI from 'element-ui';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import '../lib/element-theme/index.css';
import './assets/style.scss';

import App from './App';

import router from './router';
import store from './store/store';
import i18n from './lang/locale';

Vue.use(ElementUI, {
  i18n: (key, value) => i18n.t(key, value),
});
Vue.use(BootstrapVue);

Vue.config.productionTip = false;

/* eslint-disable no-new */
new Vue({
  el: '#app',
  store,
  i18n,
  router,
  template: '<App/>',
  components: { App },
});
