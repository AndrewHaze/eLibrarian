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
        <SeriesList :sItems="items" :sFilter="sFilter"/>
      </b-col>
      <b-col class="content">
        <books-list :sid="sid" :curSI="currentSI"></books-list>
      </b-col>
    </b-row>
  </section>
</template>

<script>
import BooksList from "../../lib/books-list";
import SeriesList from "./tab-series-s-list";
import store from "../../../store";

export default {
  name: "tab-authors",
  components: {
    BooksList,
    SeriesList
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
      sid: "tsd"
    };
  },
  mounted: function() {
    this.getSeries();
  },
  computed: {
    currentSI: function() {
      return this.$store.getters.seriesID;
    },
  },
  watch: {
    selected: function() {
      store.commit("setSeriesID", -1);
      this.sFilter = this.selected;
    },
  },
  methods: {
    getSeries() {
      const self = this;
      //Вызов функции из глобального миксина
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "с_list",
          dat: "series"
        },
        "",
        function(rd) {
          self.options = rd; //возвр. данные (Responce)
        }
      );
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "sa_list",
          dat: ""
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