import Vue from 'vue';
import VueI18n from 'vue-i18n';
import Element from 'element-ui';

import Checkout from './components/Commerce';
import {createStore} from './state/store';
import i18n from './lang/locale';

export default function main() {
    const store = createStore();

    Vue.use(Element, {
        i18n: (key, value) => i18n.t(key, value)
    });

    new Vue({
        store,
        i18n,
        components: {'checkout': Commerce}
    }).$mount('#root');
}

