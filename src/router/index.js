import Vue from 'vue';
import Router from 'vue-router';
import Home from '../components/home';
import Account from '../components/account';
import Login from '../components/login';
import Reader from '../components/reader';
import Registration from '../components/registration';
import store from '../store';

Vue.use(Router)

const ifNotAuthenticated = (to, from, next) => {
  if (!store.getters.isAuthenticated) {
    next()
    return
  }
  next('/')
}

const ifAuthenticated = (to, from, next) => {
  if (store.getters.isAuthenticated) {
    next()
    return
  }
  next('/login')
}

export default new Router({
  mode: 'history',
  routes: [{
      path: '/',
      name: 'Home',
      component: Home,
    },
    {
      path: '/reader',
      name: 'Reader',
      component: Reader,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/account',
      name: 'Account',
      component: Account,
      beforeEnter: ifAuthenticated,
    },
    {
      path: '/login',
      name: 'Login',
      component: Login,
      beforeEnter: ifNotAuthenticated,
    },
    {
      path: '/registration',
      name: 'Registration',
      component: Registration,
      beforeEnter: ifNotAuthenticated,
    },
  ],
})
