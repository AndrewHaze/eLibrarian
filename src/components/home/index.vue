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
      <modal-author-editor></modal-author-editor>
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
$header-bk-color: #abafb4;

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

/** be & ae modals — begin **/

input[type="file"] {
  display: none;
}

.highlight {
  border: 2px dashed purple;
  border-radius: 0.2rem;
  padding: 1px;
}

.cover-wrap {
  text-align: center;
  padding-right: 1.5rem !important;
  position: relative;
  min-height: 100%;
  .cover-wrap-sticky {
    position: sticky;
    top: 0;
  }
  .img-thumbnail {
    width: 100%;
  }
}

.colappse-header {
  font-weight: 600;
  overflow: hidden;
  &::after,
  &::before {
    display: inline-block;
    content: "";
    vertical-align: middle;
    box-sizing: border-box;
    width: 100%;
    height: 1px;
    background: #737b83;
    border: solid #fff;
    border-width: 0 2px;
  }
  &::after {
    margin-right: -100%;
  }
  &::before {
    margin-left: -100%;
  }
  span {
    padding-right: 0.3rem;
  }
  .btn-circle {
    position: absolute;
    right: 0;
    width: 20px;
    height: 20px;
    text-align: center;
    padding: 4px 0 4px 1px;
    font-size: 12px;
    line-height: 0;
    border-radius: 10px;
    background-color: #fff;
    margin-top: 5px;
    // &:focus {
    //   box-shadow: none;
    // }
    &:hover {
      background-color: #737b83;
    }
  }
}
.collapse-wrap {
  padding: 0.5rem 1rem 0;
  .col-fix-2btn {
    display: flex;
    flex-flow: column;
    justify-content: flex-end;
    max-width: 5.65rem;
    padding-bottom: 0.1rem;
    .ad-btn-group {
      max-height: 32px;
      margin-bottom: 15px;
      .btn {
        padding: 0.2rem 0.5rem !important;
        font-size: 0.9rem;
        &:focus {
          box-shadow: none;
        }
      }
    }
  }

  .form-group {
    > label {
      white-space: nowrap;
      .label-asterix {
        color: red;
      }
    }
  }
}

//Bootstrap form-control-sm скин для multiselect
.multiselect {
  * {
    font-size: 0.875rem;
  }
  min-height: 0;
  .multiselect__tags {
    display: flex;
    align-items: center;
    min-height: 31px;
    padding: 0 30px 0 8px;
    border-color: #ced4da;
    border-radius: 0.2rem;
    overflow: hidden;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    .multiselect__placeholder {
      margin: 0;
      line-height: 1;
      padding-top: 3px;
      min-height: 20px;
      white-space: nowrap;
      overflow: hidden;
    }
    .multiselect__tags-wrap {
      display: flex;
      flex-flow: row wrap;
      align-items: center;
      height: 100%;
      padding-top: 4px;
      .multiselect__tag {
        padding: 3px 26px 4px 10px;
        margin: 0 4px 4px 0;
        .multiselect__tag-icon {
          line-height: 19px;
        }
      }
    }
    .multiselect__input,
    .multiselect__single {
      margin: 0;
      line-height: 18px;
      padding-left: 0;
    }
    input {
      margin: 0;
    }
  }
  .multiselect__select {
    height: 100%;
    width: 30px;
  }
  .multiselect__content-wrapper {
    border-color: #ced4da;
    .multiselect__option {
      min-height: 0;
      padding: 6px 12px;
    }
    .multiselect__option--selected {
      font-weight: 600;
    }
    span:after {
      line-height: 11px;
      padding: 8px;
    }
    .multiselect__option--group {
      text-align: center;
      font-weight: 600;
    }
  }
}
.multiselect--active .multiselect__tags {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/** be & ae modals — end **/

.info-modal-icon,
.question-modal-icon,
.exclamation-modal-icon {
  font-size: 2.6rem;
  margin: 0 0 0.25rem;
}

.info-modal-icon,
.question-modal-icon {
  color: #007bff;
}

.exclamation-modal-icon {
  color: #dc3545;
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
import ModalAuthorEditor from "./modal-ae";
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
    ModalAuthorEditor,
    AuthorsTab,
    GenresTab,
    SeriesTab,
    LibraryTab
  },
  data() {
    return {
      //флаг приветствия при регистрации
      congShow: false,
      tabIndex: 0,
      deleteBookFlag: false
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
      let dbFlag = this.$store.getters.deleteBookFlag;
      if (dbFlag) {
        var lId = this.$store.getters.librarySID;
        var aId = this.$store.getters.authorID;
        var gId = this.$store.getters.genresID;
        var sId = this.$store.getters.seriesID;
      }
      store.commit("setLibrarySID", -1);
      store.commit("setAuthorID", -1);
      store.commit("setGenresID", -1);
      store.commit("setSeriesID", -1);

      if (dbFlag) {
        this.$nextTick(function() {
          store.commit("setLibrarySID", lId);
          store.commit("setAuthorID", aId);
          store.commit("setGenresID", gId);
          store.commit("setSeriesID", sId);
        });
      }

      if (this.$refs.aTab) {
        this.$refs.aLib.getLibraryS();
        this.$refs.aTab.getAuthors();
        this.$refs.gTab.getGenres();
        this.$refs.sTab.getSeries();
      }
      store.commit("setDeleteBookFlag", false);
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
    },
    update: function() {
      return this.$store.getters.stimulusValue;
    },
    updateLib: function() {
      return this.$store.getters.stimulusValueForLib;
    }
  },
  watch: {
    tabIndex: function(val) {
      store.commit("setCurrentTab", val);
    },
    update() {
      this.deleteBookFlag = true;
      this.updateAll();
    },
    updateLib: function() {
      let lId = this.$store.getters.librarySID;
      store.commit("setLibrarySID", -1);
      this.$nextTick(function() {
        store.commit("setLibrarySID", lId);
      });
      //обновляем другие вкладки, кроме текущей
      let aId = this.$store.getters.authorID;
      let gId = this.$store.getters.genresID;
      let sId = this.$store.getters.seriesID;
      switch (this.tabIndex) {
        case 0:
          store.commit("setAuthorID", -1);
          store.commit("setGenresID", -1);
          store.commit("setSeriesID", -1);
          this.$nextTick(function() {
            store.commit("setAuthorID", aId);
            store.commit("setGenresID", gId);
            store.commit("setSeriesID", sId);
          });
          break;
        case 1:
          store.commit("setGenresID", -1);
          store.commit("setSeriesID", -1);
          this.$nextTick(function() {
            store.commit("setGenresID", gId);
            store.commit("setSeriesID", sId);
          });
          break;
        case 2:
          store.commit("setAuthorID", -1);
          store.commit("setSeriesID", -1);
          this.$nextTick(function() {
            store.commit("setAuthorID", aId);
            store.commit("setSeriesID", sId);
          });
          break;
        case 3:
          store.commit("setAuthorID", -1);
          store.commit("setGenresID", -1);
          this.$nextTick(function() {
            store.commit("setAuthorID", aId);
            store.commit("setGenresID", gId);
          });
          break;      
      }
    }
  }
};
</script>
