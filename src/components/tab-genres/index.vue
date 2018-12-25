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
      <b-col class="sidebar sidebar-genres ml-3 p-0">
        <GenresTree :gItems="items"/>
      </b-col>
      <b-col class="content">Книги здесь...</b-col>
    </b-row>
  </section>
</template>

<script>
//import BooksList from "../tab-authors-b-list";
import GenresTree from "../tab-genres-g-tree";
import store from "../../store";

export default {
  name: "tab-authors",
  components: {
    //BooksList,
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
      sid: "tsg"
    };
  },
  mounted: function() {
    this.getSeries();
  },
  computed: {
    currentOC: function() {
      return this.$store.getters.orderCode;
    }
  },
  watch: {
    currentOC: function() {
      this.getSeries();
    },
    selected: function() {
      //store.commit("setSeriesID", -1);
      this.gFilter = this.selected;
      this.getSeries();
    }
  },
  methods: {
    getSeries() {
      const self = this;
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
      console.log(self.currentOC)
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "g_list",
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
  .sidebar-genres {
    flex: 0 0 29rem !important;
  }

</style>