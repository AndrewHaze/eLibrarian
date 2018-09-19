<template>
  <div id="app">
    <header>
      <!-- Fixed navbar -->
      <b-navbar type="dark" variant="dark fixed-top" toggleable>
        <b-navbar-toggle target="nav_dropdown_collapse"></b-navbar-toggle>
        <b-collapse is-nav id="nav_dropdown_collapse">
          <b-navbar-nav>
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
    <b-container fluid class="d-flex flex-column cf100">
        <router-view/>
      <!--  -->
    </b-container>
    <footer class="footer">
      <div class="container">
        <span class="text-muted">Place sticky footer content here.</span>
      </div>
    </footer>
  </div>
</template>

<script>
import { USER_REQUEST } from "./store/actions/user";

export default {
  created: function() {
    if (this.$store.getters.isAuthenticated) {
      this.$store.dispatch(USER_REQUEST);
    }
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
  & > div {
    height: 100%;
  }
}

.footer {
  position: absolute;
  bottom: 0;
  width: 100%;
  /* Set the fixed height of the footer here */
  height: 60px;
  line-height: 60px;
  /* Vertically center the text there */
  color: lightgrey;
  background-color: #343a40;
}

/* Custom page CSS
        -------------------------------------------------- */

/* Not required for template or sticky footer method. */

.footer > .container {
  padding-right: 15px;
  padding-left: 15px;
}

code {
  font-size: 80%;
}
</style>
