<template>
  <section>
    <loading v-if="loading" />
    <div v-if="isAuthenticated">
      <b-modal v-model="congShow" no-close-on-backdrop ok-only size="lg" :title="appTitle">
        <div class="d-block fileName-center">
          <h4>Поздравляем! Вы успешно зарегистрировались!</h4>
        </div>
      </b-modal>
      <ModalBooksScanner/>
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
// для табов
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
import { mapGetters } from "vuex";
import store from "../../store";
import Login from "../login";
import ModalBooksScanner from "../modal-bs";
import AuthorsTab from "../tab-authors";
import GenresTab from "../tab-genres";
import SeriesTab from "../tab-series";

export default {
  name: "home",
  components: {
    Login,
    ModalBooksScanner,
    AuthorsTab,
    GenresTab,
    SeriesTab
  },
  data() {
    return {
      congShow: false
    };
  },
  created: function() {
    if (this.congratulation) {
      this.congShow = true;
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
  }
};
</script>

