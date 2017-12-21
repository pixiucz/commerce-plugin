/* eslint-disable no-param-reassign */

import Vue from 'vue';
import VueResource from 'vue-resource';

import BootstrapVue from 'bootstrap-vue';
import ElementUI from 'element-ui';
import 'font-awesome/scss/font-awesome.scss';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import '../lib/element-theme/index.css';
import './assets/style.scss';

import App from './App';

import router from './router';
import store from './store/store';
import i18n from './lang/locale';

// GLOBAL MIXINS
import ClosableSidebar from './mixins/ClosableSidebar';

Vue.use(VueResource);
Vue.use(ElementUI, {
  i18n: (key, value) => i18n.t(key, value),
});
Vue.use(BootstrapVue);

// GLOBAL MIXINS (FIXME: move to file?)
Vue.mixin(ClosableSidebar);

// GLOBAL FILTERS (FIXME: move to file?)
Vue.filter('price', value => `${(value / 100).toFixed(2)} €`);

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
