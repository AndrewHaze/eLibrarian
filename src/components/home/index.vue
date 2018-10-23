<template>
  <section>
    <loading v-if="loading" />
    <div v-if="isAuthenticated">
      <b-modal v-model="congShow" no-close-on-backdrop ok-only :title="appTitle">
        <div class="d-block text-center">
          <h3>Поздравляем! Вы успешно зарегистрировались!</h3>
        </div>
      </b-modal>
      <b-modal id="bookScanner" no-close-on-backdrop hide-footer size="lg" title="Импорт книг">
        <b-container fluid>
          <b-row class="mb-1">
            <b-col>
              <b-button-toolbar key-nav aria-label="Toolbar with button groups">
                <b-button-group class="e-btn-group mr-2">
                  <b-button variant="primary">Обработать папку</b-button>
                  <b-button variant="success">Добавить папку</b-button>
                  <b-button variant="success">Добавить файлы</b-button>
                </b-button-group>
                <b-button variant="danger">Запуск</b-button>
              </b-button-toolbar>
            </b-col>
          </b-row>
        </b-container>
      </b-modal>
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
    <div v-if="!isAuthenticated && authStatus !== 'loading'" class="h-100">
      <login/>
    </div>
  </section>
</template>

<style lang="scss">
  .fix-height {
    height: calc(100vh - 225px);
  }
  
  .sidebar {
    display: flex;
    flex-flow: column nowrap;
    flex: 0 0 230px !important;
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
  
  .e-btn-group {
    &>.btn+.btn {
      margin-left: 1px;
    }
    
  }
</style>

<script>
  import {
    mapGetters
  } from 'vuex';
  import store from "../../store";
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
      ...mapGetters(['isAuthenticated', 'authStatus', 'congratulation', 'appTitle']),
      loading: function() {
        return this.authStatus === 'loading' && !this.isAuthenticated
      },
  
    },
    created: function() {
      if (this.congratulation) {
        this.congShow = true;
      }
    },
    methods: {
      // openFiles: function() {
      //   let file_input = document.createElement("input");
      //   file_input.type = "file";
      //   file_input.click();
      // },
    },
    data() {
      return ({
        congShow: false,
      })
    },
  }
</script>

