import Vue from 'vue';
import VueI18n from 'vue-i18n';

import skElementLocale from 'element-ui/lib/locale/lang/sk';

import skLocale from './sk';

const messages = {
  sk: {
    ...skLocale,
    ...skElementLocale,
  },
};

Vue.use(VueI18n);

const i18n = new VueI18n({
  locale: 'sk',
  messages,
});

export default i18n;
