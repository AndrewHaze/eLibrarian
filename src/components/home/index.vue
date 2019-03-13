<template>
  <section>
    <loading v-if="loading"/>
    <div v-if="isAuthenticated">
      <b-modal v-model="congShow" no-close-on-backdrop ok-only size="lg" :title="appTitle">
        <b-row>
          <b-col md="1">
            <font-awesome-icon class="info-modal-icon" icon="question-circle"/>
          </b-col>
          <b-col>
            <h4 class="mt-1">Поздравляем! Вы успешно зарегистрировались!</h4>
          </b-col>
        </b-row>
      </b-modal>
      <modal-books-scanner></modal-books-scanner>
      <modal-book-editor></modal-book-editor>
      <b-row class="z100">
        <b-col>
          <b-tabs v-model="tabIndex">
            <b-tab title="Библиотека">
              <library-tab ref="aLib"></library-tab>
            </b-tab>
            <b-tab title="Авторы">
              <authors-tab ref="aTab"></authors-tab>
            </b-tab>
            <b-tab title="Жанры">
              <genres-tab ref="gTab"></genres-tab>
            </b-tab>
            <b-tab title="Серии">
              <series-tab ref="sTab"></series-tab>
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
$line-color: #dee2e6;
$header-font-color: #495057;
$selected-color: #ddd;
$hover-color: rgba(221, 221, 221, 0.4);

%flex {
  display: flex;
  flex: row, nowrap;
  justify-content: space-between;
}

.fix-height {
  height: calc(100vh - 240px);
}

.sidebar {
  display: flex;
  flex-flow: column nowrap;
  flex: 0 0 29rem !important;
  min-width: 29rem;
  height: 100%;
}

// для табов
.content {
  height: 100%;
  overflow-y: auto;
}

.control-element {
  cursor: pointer;
  user-select: none;
}

.modal-max {
  max-width: 80% !important;
  min-width: 680px;

  @media (max-width: 576px) {
    max-width: none !important;
  }
}

.info-modal-icon {
  color: #007bff;
  font-size: 2.6rem;
  margin: 0 0 0.25rem;
}

.question-modal-icon {
  color: #007bff;
  font-size: 2.6rem;
  margin: 0 0 0 0.25rem;
}

.table-wrap {
  user-select: none;
  border: 1px solid $line-color;
  .modal-tb-header {
    @extend %flex;
    cursor: pointer;
    position: relative;
    width: 100%;
    padding: 0.4rem 0.75rem 0.5rem;
    border-bottom: 1px solid $line-color;
    .modal-tb-header-left {
      font-weight: 600;
      color: $header-font-color;
    }
    .modal-tb-header-right-asc,
    .modal-tb-header-right-desc {
      position: absolute;
      top: 0.3rem;
      opacity: 0.4;
    }
    .modal-tb-header-right-asc {
      right: 1rem;
    }
    .modal-tb-header-right-desc {
      right: 0.5rem;
    }
    .active {
      opacity: 1;
    }
  }

  .table-body-wrap {
    width: 100%;
    overflow: auto;
    .table {
      margin: 0;
      td {
        padding: 0.35rem 0.75rem 0.4rem;
        border: none;
        cursor: pointer;
      }
      .active {
        background-color: $selected-color;
      }
    }
  }

  .b-table-head {
    th {
      padding: 0;
      overflow: hidden;
      border: 0 !important;
    }
  }
}

.tba,
.tbs,
.tbg {
  display: flex;
  flex-flow: column nowrap;
  flex: 1 1 auto;
  height: 100%;
  user-select: none;

  .tb-header {
    @extend %flex;
    border: 1px solid $line-color;
    padding: 0.5rem 0.3rem 0.5rem 0.65rem;

    .tb-header-left,
    .tb-header-right {
      cursor: pointer;
      @extend %flex;
    }

    .tb-header-left {
      width: 360px;
    }

    .tb-header-title {
      font-weight: 600;
      color: $header-font-color;
    }

    .tb-header-sort-arrows {
      position: relative;
      width: 1.5rem;

      .tb-header-sort-desc,
      .tb-header-sort-asc {
        position: absolute;
        margin: 0;
        padding: 0;
        opacity: 0.4;
        top: 0;
      }

      .tb-header-sort-desc {
        right: 8px;
      }

      .tb-header-sort-asc {
        right: 0px;
      }

      .active {
        opacity: 1;
      }
    }
  }

  .tb-body {
    overflow: auto;
    height: calc(100% - 70px);

    .tb-body-element {
      @extend %flex;
      border-left: 1px solid $line-color;
      border-right: 1px solid $line-color;
      padding: 0.4rem 0.3rem 0.4rem 0.65rem;
      cursor: pointer;
      line-height: 1.2rem;
      &:hover {
        background-color: $hover-color;
      }

      &:last-child {
        border-bottom: 1px solid $line-color;
      }

      .tb-body-element-right {
        display: flex;
        min-width: 4rem;
        justify-content: center;
        align-items: center;
      }
    }

    .active {
      background-color: $selected-color;

      &:hover {
        background-color: $selected-color;
      }
    }
  }
}
</style>

<script>
import { mapGetters } from "vuex";
import store from "../../store";
import Login from "../login";
import ModalBooksScanner from "./modal-bs";
import ModalBookEditor from "./modal-be";
import AuthorsTab from "./tab-authors";
import GenresTab from "./tab-genres";
import SeriesTab from "./tab-series";
import LibraryTab from "./tab-library";

export default {
  name: "home",
  components: {
    Login,
    ModalBooksScanner,
    ModalBookEditor,
    AuthorsTab,
    GenresTab,
    SeriesTab,
    LibraryTab
  },
  data() {
    return {
      //флаг приветствия при регистрации
      congShow: false,
      tabIndex: 0
    };
  },
  created: function() {
    if (this.congratulation) {
      this.congShow = true;
    }
    if (this.storageAvailable("localStorage") && localStorage.currentTab) {
      store.commit("setCurrentTab", localStorage.currentTab);
      this.tabIndex = parseInt(localStorage.currentTab, 10);
    } else this.tabIndex = 0;
    this.updateAll();
  },
  methods: {
    updateAll() {
      store.commit("setLibrarySID", -1);
      store.commit("setAuthorID", -1);
      store.commit("setGenresID", -1);
      store.commit("setSeriesID", -1);
      if (this.$refs.aTab) {
        this.$refs.aLib.getLibraryS();
        this.$refs.aTab.getAuthors();
        this.$refs.gTab.getGenres();
        this.$refs.sTab.getSeries();
      }
    }
  },
  computed: {
    ...mapGetters([
      "isAuthenticated",
      "authStatus",
      "congratulation",
      "appTitle"
    ]),
    loading: function() {
      return this.authStatus === "loading" && !this.isAuthenticated;
    }
  },
  watch: {
    tabIndex: function(val) {
      store.commit("setCurrentTab", val);
    }
  }
};
</script>
