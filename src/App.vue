<template>
  <div id="app">
    <header>
      <!-- Fixed navbar -->
      <b-navbar type="dark" variant="dark fixed-top">
        <b-navbar-toggle target="nav_dropdown_collapse"></b-navbar-toggle>
        <b-collapse is-nav id="nav_dropdown_collapse">
          <b-navbar-nav v-if="isProfileLoaded">
            <!-- Navbar dropdowns -->
            <b-nav-item v-if="!isReader" v-b-modal.bookScanner>Импорт книг</b-nav-item>
            <b-nav-item v-if="isReader" @click="backClick">Назад</b-nav-item>
          </b-navbar-nav>
        </b-collapse>
        <b-navbar-brand href="#">{{this.$store.getters.appTitle}}</b-navbar-brand>
      </b-navbar>
    </header>
    <!-- Begin page content -->
    <b-container fluid class="d-flex flex-column cf100 h-100">
      <router-view/>
      <!--  -->
    </b-container>
    <footer class="footer">
      <b-container fluid>
        <b-row v-if="isProfileLoaded && !isReader">
          <b-col class="text-right">
            <b-form-radio-group
              id="btnradios2"
              buttons
              v-model="picked"
              size="sm"
              name="radiosBtnDefault2"
            >
              <b-form-radio value="cover" title="Режим отображения: «Плитка»">
                <font-awesome-icon icon="th"/>
              </b-form-radio>
              <b-form-radio value="tree" title="Режим отображения: «Дерево»">
                <font-awesome-icon icon="stream"/>
              </b-form-radio>
              <b-form-radio value="table" title="Режим отображения: «Таблица»">
                <font-awesome-icon icon="table"/>
              </b-form-radio>
            </b-form-radio-group>
          </b-col>
          <b-col class="col-auto text-right">
            <b-form-checkbox-group v-model="selectedCheckbox" buttons size="sm" name="buttons2">
              <b-form-checkbox value="on" :title="this.$store.getters.iPanel ?  'Скрыть с информацией о книге или авторе' : 'Показать панель с информацией о книге или авторе'">
                <font-awesome-icon icon="tag"/>
              </b-form-checkbox>
            </b-form-checkbox-group>
          </b-col>

          <b-col class="col-auto text-right" title="Текущий пользователь">
            <b-img :src="require('./assets/owner.png')" height="29" class="mr-1"/>
            <b-dropdown id="ddown1" dropup right v-bind:text="name" size="sm">
              <b-dropdown-item class="m0" v-if="isAuthenticated" @click="logout" title="Сменить пользователя">Выйти</b-dropdown-item>
            </b-dropdown>
          </b-col>
        </b-row>
      </b-container>
    </footer>
  </div>
</template>

<script>
import { USER_REQUEST } from "./store/actions/user";
import { mapGetters, mapState } from "vuex";
import { AUTH_LOGOUT } from "./store/actions/auth";
import store from "./store";

export default {
  data: function() {
    return {
      picked: "",
      selectedCheckbox: ['on']
    };
  },
  created: function() {
    if (this.$store.getters.isAuthenticated) {
      this.$store.dispatch(USER_REQUEST);
    }
    if ((this.storageAvailable('localStorage')) && localStorage.lookOfBookList) {
      store.commit("setblLook", localStorage.lookOfBookList);
      this.picked = localStorage.lookOfBookList;
    } else this.picked = "cover";
    if ((this.storageAvailable('localStorage')) && localStorage.InfoPanel) {
      store.commit("setblLook", ['on']);
      this.selectedCheckbox = ['on'];
    } else this.selectedCheckbox = null;
  },
  methods: {
    logout: function() {
      this.$store.dispatch(AUTH_LOGOUT).then(() => this.$router.push("/login"));
    },
    backClick: function() {
      store.commit("setReader", false);
      this.$router.push("/");
    },
    
  },
  computed: {
    ...mapGetters([
      "getProfile",
      "isAuthenticated",
      "isProfileLoaded",
      "ownerName",
      "isReader"
    ]),
    ...mapState({
      authLoading: state => state.auth.status === "loading",
      name: state => `${state.user.profile.name}`
      //name: state => `${state.auth.owner}`
    })
  },
  watch: {
    picked: function(val) {
      store.commit("setblLook", val);
    },
    selectedCheckbox: function() {
      store.commit("setInfoPanel", this.selectedCheckbox[0]);
    }  
  }
};
</script>

<style lang="scss">
$min-width: 860px;


html, body {
  width: 100%;
}

html {
  position: relative;
  min-height: 100%;
}

body {
  /* Margin bottom by footer height */
  margin-bottom: 60px;
  height: calc(100vh - 60px);
  box-sizing: border-box;
  & > div {
    height: 100%;
  }
}

body>header, body, body>footer {
  min-width: $min-width;
}

.navbar {
  min-width: $min-width - 20;
}

section {
  height: 100%;
}

* {
  outline: 0 !important;
}

.cf100 {
  padding: 75px 15px 0;
  box-sizing: border-box;
}

.loading-screen {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  background-color: #fff;
  z-index: 100;
  display: flex;
  justify-content: center;
  align-items: center;
  padding-bottom: 30%;
  font-size: 1.2rem;
}

.footer {
  position: absolute;
  bottom: 0;
  width: 100%;
  /* Set the fixed height of the footer here */
  height: 60px;
  /* Vertically center the text there */
  color: lightgrey;
  background-color: #343a40;
  padding: 16px 0;
  //z-index: 200;
}

.footer > .container {
  padding-right: 15px;
  padding-left: 15px;
}

code {
  font-size: 80%;
}

#ddown1 > button {
  min-width: 80px;
}
</style>
