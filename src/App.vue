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
            <b-nav-item-dropdown text="Пользователь">
              <b-dropdown-item href="#">Account</b-dropdown-item>
              <b-dropdown-item href="#">Settings</b-dropdown-item>
            </b-nav-item-dropdown>
          </b-navbar-nav>
        </b-collapse>
        <b-navbar-brand href="#">eLibrarian v.0.0.1</b-navbar-brand>
      </b-navbar>
    </header>
    <!-- Begin page content -->
    <b-container fluid class="d-flex flex-column cf100 h-100">
      <b-row class="z100 h-100">
        <b-col class="h-100">
          <b-tabs class="h-100">
            <b-tab title="Авторы" active>
              <AuthorsTab />
            </b-tab>
            <b-tab title="Жанры">
              <GenresTab />
            </b-tab>
            <b-tab title="Серии">
              <SeriesTab />
            </b-tab>
          </b-tabs>
        </b-col>
      </b-row>
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
  import AuthorsTab from "./components/AuthorsTab.vue";
  import GenresTab from "./components/GenresTab.vue";
  import SeriesTab from "./components/SeriesTab.vue";
  
  export default {
    components: {
      AuthorsTab,
      GenresTab,
      SeriesTab
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
  
  .footer>.container {
    padding-right: 15px;
    padding-left: 15px;
  }
  
  code {
    font-size: 80%;
  }
  
  .cf100 {
    padding: 75px 15px 0;
    box-sizing: border-box;
  }
  
  .fix-height {
    height: calc(100vh - 225px);
  }
  
  .sidebar {
    display: flex;
    flex-flow: column nowrap;
    -ms-flex: 0 0 230px;
    flex: 0 0 230px;
    /*border-right: 1px solid #dee2e6;*/
    height: 100%;
    overflow-y: auto;
  }
  
  .content {
    height: 100%;
    overflow-y: auto;
  }
  
  .control-element {
    cursor: pointer;
    user-select: none;
  }
</style>
