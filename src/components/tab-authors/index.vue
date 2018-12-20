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
        <AuthorsList :aItems="items" :aFilter="aFilter"/>
      </b-col>
      <b-col class="content">
        <books-list :sid="sid" :curAI="currentAI"></books-list>
      </b-col>
    </b-row>
  </section>
</template>

<script>
import BooksList from "../tab-authors-b-list";
import AuthorsList from "../tab-authors-a-list";
import store from "../../store";

export default {
  name: "tab-authors",
  components: {
    BooksList,
    AuthorsList
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
      aFilter: "*",
      sid: "tad"
    };
  },
  mounted: function() {
    this.getAuthors();
  },
  computed: {
    currentAI: function() {
      return this.$store.getters.authorID;
    }
  },
  watch: {
    selected: function() {
      store.commit("setAuthorID", -1);
      this.aFilter = this.selected;
    },
  },
  methods: {
    getAuthors() {
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
