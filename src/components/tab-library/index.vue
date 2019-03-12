<template>
  <section>
    <b-row class="fix-height">
      <b-col class="sidebar ml-3 p-0">
        
      </b-col>
      <b-col class="content">
        <books-list :sid="sid" :curSLI="currentSLI"></books-list>
      </b-col>
    </b-row>
  </section>
</template>

<script>
import BooksList from "../tab-authors-b-list";

import store from "../../store";

export default {
  name: "tab-library",
  components: {
    BooksList
  },
  data: function() {
    return {
      info: null,
      loading: true,
      errored: false,
      //Массив фильтра авторов
      options: [],
      //Массив авторов
      items: [],
      sid: "tld"
    };
  },
  mounted: function() {
    this.getLibraryS();
  },
  computed: {
    currentSLI: function() {
      return this.$store.getters.librarySID;
    }
  },
  
  methods: {
    getLibraryS() {
      const self = this;
      //Вызов функции из глобального миксина
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "с_list",
          dat: "authors"
        },
        "",
        function(rd) {
          self.options = rd; //возвр. данные (Responce)
        }
      );
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "a_list",
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
