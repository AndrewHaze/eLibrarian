<template>
  <section>
    <b-row>
      <b-col class="mx-3 px-0 pt-3">
        <b-form-group>
          <b-form-radio-group
            id="btnradios1"
            buttons
            v-model="selected"
            size="sm"
            :options="options"
            name="radiosBtnDefault1"
          ></b-form-radio-group>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="fix-height">
      <b-col class="sidebar ml-3 p-0">
        <GenresTree :gItems="items"/>
      </b-col>
      <b-col class="content">
        <books-list :sid="sid" :curGI="currentGI"></books-list>
      </b-col>
    </b-row>
  </section>
</template>

<script>
import BooksList from "../tab-authors-b-list";
import GenresTree from "../tab-genres-g-tree";
import store from "../../store";

export default {
  name: "tab-authors",
  components: {
    BooksList,
    GenresTree
  },
  data: function() {
    return {
      info: null,
      loading: true,
      errored: false,
      //Массив фильтра авторов
      selected: "*",
      options: [],
      //Массив авторов
      items: [],
      sFilter: "*",
      sid: "tgd"
    };
  },
  mounted: function() {
    this.getGenres();
  },
  computed: {
    //текущая серия
    currentGI: function() {
      return this.$store.getters.genresID;
    },
    //порядок сортировки
    currentOC: function() {
      return this.$store.getters.orderCode;
    }
  },
  watch: {
    currentOC: function() {
      this.getGenres();
    },
    selected: function() {
      store.commit("setGenresID", -1);
      this.gFilter = this.selected;
      this.getGenres();
    }
  },
  methods: {
    getGenres() {
      const self = this;
      let id = this.$store.getters.genresID;
      let type = this.$store.getters.genresType;
      //Вызов функции из глобального миксина
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "с_list",
          dat: "genres"
        },
        "",
        function(rd) {
          self.options = rd; //возвр. данные (Responce)
        }
      );
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "g_list",
          id: id,
          type: type,
          filter: self.gFilter,
          order: self.currentOC
        },
        "",
        function(rd) {
          self.items = rd; //возвр. данные (Responce)
        }
      );
    }
  }
};
</script>

<style lang='scss'>
</style>