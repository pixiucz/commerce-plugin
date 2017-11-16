import Vue from 'vue';
import VueI18n from 'vue-i18n';

import enElementLocale from 'element-ui/lib/locale/lang/en';
import czElementLocale from 'element-ui/lib/locale/lang/cz';
import skElementLocale from 'element-ui/lib/locale/lang/sk';

import czLocale from './cz';
import enLocale from './en';
import skLocale from './sk';

const messages = {
    en: {
        ...enLocale,
        ...enElementLocale
    },
    cz: {
        ...czLocale,
        ...czElementLocale
    },
    sk: {
        ...skLocale,
        ...skElementLocale
    }
};

Vue.use(VueI18n);

const i18n = new VueI18n({
    locale: 'sk',
    messages
});

export default i18n;

