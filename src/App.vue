<template>
  <div id="app">
    <header>
      <!-- Fixed navbar -->
      <b-navbar type="dark" variant="dark fixed-top" toggleable>
        <b-navbar-toggle target="nav_dropdown_collapse"></b-navbar-toggle>
        <b-collapse is-nav id="nav_dropdown_collapse">
          <b-navbar-nav v-if="isProfileLoaded">
            <!-- Navbar dropdowns -->
            <b-nav-item-dropdown text="Файл">
              <b-dropdown-item href="#">EN</b-dropdown-item>
              <b-dropdown-item href="#">ES</b-dropdown-item>
              <b-dropdown-item href="#">RU</b-dropdown-item>
              <b-dropdown-item href="#">FA</b-dropdown-item>
            </b-nav-item-dropdown>
          </b-navbar-nav>
        </b-collapse>
        <b-navbar-brand href="#">eLibrarian v.0.0.1</b-navbar-brand>
      </b-navbar>
    </header>
    <!-- Begin page content -->
    <b-container fluid class="d-flex flex-column cf100 h-100">
      <router-view/>
      <!--  -->
    </b-container>
    <footer class="footer">
      <b-container fluid>
        <b-row>
          <b-col class="text-right" v-if="isProfileLoaded">
            <b-img :src="require('./assets/owner.png')" height="29" class="mr-1" />
            <b-dropdown id="ddown1" dropup right v-bind:text="name" size="sm">
              <b-dropdown-item class="m0" v-if="isAuthenticated" @click="logout">Выйти</b-dropdown-item>
            </b-dropdown>
          </b-col>
        </b-row>
      </b-container>
    </footer>
  </div>
</template>

<script>
  import {
    USER_REQUEST
  } from "./store/actions/user";
  import {
    mapGetters,
    mapState
  } from "vuex";
  import {
    AUTH_LOGOUT
  } from "./store/actions/auth";
  
  export default {

    created: function() {
      if (this.$store.getters.isAuthenticated) {
        this.$store.dispatch(USER_REQUEST);
      }
    },
    methods: {
      logout: function() {
        this.$store.dispatch(AUTH_LOGOUT).then(() => this.$router.push("/login"));
      }
    },
    computed: {
      ...mapGetters(["getProfile", "isAuthenticated", "isProfileLoaded", "ownerName"]),
      ...mapState({
        authLoading: state => state.auth.status === "loading",
        name: state => `${state.user.profile.name}`
        //name: state => `${state.auth.owner}`
      }),
      
    }
  };
</script>

<style lang="scss">
  html {
    position: relative;
    min-height: 100%;
  }
  
  body {
    /* Margin bottom by footer height */
    margin-bottom: 60px;
    height: calc(100vh - 60px);
    box-sizing: border-box;
    &>div {
      height: 100%;
    }
  }
  
  section {
    height: 100%;
  }
  
  .cf100 {
    padding: 75px 15px 0;
    box-sizing: border-box;
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
  }
  
  .footer>.container {
    padding-right: 15px;
    padding-left: 15px;
  }
  
  code {
    font-size: 80%;
  }
  
  #ddown1>button {
    min-width: 80px;
  }
</style>
