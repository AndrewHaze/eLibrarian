import Vue from 'vue';
import App from './App.vue'
import router from './router'
import store from './store'
import { AUTH_LOGOUT } from "./store/actions/auth";
import VJstree from 'vue-jstree'
import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import axios from "axios"
import Loading from './components/lib/loading'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTh, faStream, faTable, faCheck, faHeart, faBell, faTrashAlt, faBookReader, faEdit, faStar, faStarHalfAlt, faInfoCircle, faCalendarCheck, faTag, faAddressCard, faUserFriends, faUserMinus, faUserTimes, faTimes, faQuestionCircle, faChevronUp, faChevronDown, faPlus, faMinus, faCalendar, faCalendarWeek, faCalendarDay, faCalendarAlt, faClock } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faTh, faStream, faTable, faCheck, faHeart, faBell, faTrashAlt, faBookReader, faEdit, faStar, faStarHalfAlt, faInfoCircle, faCalendarCheck, faTag, faAddressCard, faUserFriends, faUserMinus, faUserTimes, faTimes, faQuestionCircle, faChevronUp, faChevronDown, faPlus, faMinus, faCalendar, faCalendarWeek, faCalendarDay, faCalendarAlt, faClock)
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('loading', Loading)

Vue.use(VJstree)
Vue.use(BootstrapVue)

Vue.config.productionTip = false

Vue.mixin({
  methods: {
    setServerError(m, d) {
      console.log("******* db_api call *******");
      console.log('Message: ', m);
      console.log('Data: ', d);
      console.log("***************************");
      return;
    },
    makeToast(message, variant = null) {
      this.$bvToast.toast(message, {
        title: this.$store.getters.appTitle,
        variant: variant,
        //autoHideDelay: 2000,
        solid: true
      })
    },
    callApi(url, prms, hct, callback) {
      axios({
        method: "post", //отправка файла pdf
        url: this.$store.getters.prefix + "/static/api.php",
        data: { cmd: 'check' },
        withCredentials: true, //передаем куки
        headers: {
          "content-type": "application/x-www-form-urlencoded"
        }
      })
        .then(response => {
          if ((response.data.data != sessionStorage.getItem('user-login')) && this.$store.getters.isAuthenticated) {
            this.$store.dispatch(AUTH_LOGOUT).then(() => this.$router.push("/login"));
            return true
          }
        })
        .catch(error => {
          this.setServerError(error.message, error.stack);
          this.errored = true;
        });
      axios({
        method: "post",
        url: url,
        data: prms,
        withCredentials: true, //передаем куки
        headers: { 'content-type': hct || 'application/x-www-form-urlencoded' },
      })
        .then(response => {
          // в response.data получаем JSON,
          // сервер формирует обязательные поля data,success,error
          let rspData = response.data;
          if (!rspData.success) {
            this.setServerError(rspData.error, "");
          } else {
            callback(rspData.data);
          }
        })
        .catch(error => {
          this.setServerError(error.message, error.stack);
          this.errored = true;
        })             
        .finally(() => (this.loading = false));
    },
    //
    getViewportHeight(doc) {
      doc = doc || document;
      var elem =
        doc.compatMode == "CSS1Compat" ? doc.documentElement : doc.body;
      return elem.clientHeight;
    },
    storageAvailable: function (type) {
      try {
        var storage = window[type],
          x = '__storage_test__';
        storage.setItem(x, x);
        storage.removeItem(x);
        return true;
      }
      catch (e) {
        return e instanceof DOMException && (
          // everything except Firefox
          e.code === 22 ||
          // Firefox
          e.code === 1014 ||
          // test name field too, because code might not be present
          // everything except Firefox
          e.name === 'QuotaExceededError' ||
          // Firefox
          e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
          // acknowledge QuotaExceededError only if there's something already stored
          storage.length !== 0;
      }
    },
  }
})

new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: {
    App
  }
})
