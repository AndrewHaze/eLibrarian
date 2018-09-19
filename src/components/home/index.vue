<template>
  <div>
    <loading v-if="loading"/>
    <div v-if="isAuthenticated">
      <b-row class="z100">
        <b-col>
          <b-tabs>
            <b-tab title="Авторы" active>
              <AuthorsTab/>
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
    </div>
    <div v-if="!isAuthenticated && authStatus !== 'loading'">
      <h1>Welcome to DogeBook !</h1>
      <p>When meeting new doge friends is harder than ever, Dogebook closes the gap between all paws in the world</p>
      <login/>
    </div>
  </div>
</template>

<style lang="scss">
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

<script>
  import { mapGetters } from 'vuex';
  import Login from '../login';
  import AuthorsTab from "../tab-authors";
  import GenresTab from "../tab-genres";
  import SeriesTab from "../tab-series";

  export default {
    name: 'home',
    components: {
      Login,
      AuthorsTab,
      GenresTab,
      SeriesTab
    },
    computed: {
      ...mapGetters(['isAuthenticated', 'authStatus']),
      loading: function () {
        return this.authStatus === 'loading' && !this.isAuthenticated
      }
    },
    data () {
      return ({ })
    },
  }
</script>
