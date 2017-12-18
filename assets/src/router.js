import Vue from 'vue';
import Router from 'vue-router';

import Home from './pages/Home';
import Category from './pages/Category';
import Detail from './pages/Detail';

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
    },
    {
      path: '/kategoria/:slug',
      name: 'category',
      component: Category,
      props: true,
    },
    {
      path: '/produkt/:slug',
      name: 'detail',
      component: Detail,
      props: true,
    },
    // BEWARE that following routes are not source-of-thruth, but merely set flags
    {
      path: '/objednavky',
      name: 'orders',
      component: Home,
      children: [
        {
          path: '/:id',
          name: 'order',
          component: Home,
        },
      ],
    },
    {
      path: '/kosik',
      name: 'cart',
      component: Home,
    },
    {
      path: '/uzivatel',
      name: 'user',
      component: Home,
    },
    // END of warning
    {
      path: '*',
      redirect: { name: 'home' },
    },
  ],
});
