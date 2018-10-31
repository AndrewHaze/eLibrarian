import Vue from 'vue';
import App from './App.vue'
import router from './router'
import store from './store'
import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import axios from "axios"

import Loading from './components/lib/loading'

Vue.use(BootstrapVue)
Vue.component('loading', Loading)

Vue.config.productionTip = false

Vue.mixin({
  methods: {
    setServerError(m, d) {
      console.log("******* db_api call *******");
      console.log('Message: ',m);
      console.log('Data: ',d);
      console.log("***************************");
      return;
    },
    callApi(url, prms, hct, callback) {
      axios({
          method: "post",
          url: url,
          data: prms,
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
    }
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
