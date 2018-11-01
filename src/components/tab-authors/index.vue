<template>
  <section>
    <b-row>
      <b-col class="mx-3 px-0 pt-3">
        <b-form-group>
          <b-form-radio-group id="btnradios1"
                              buttons
                              v-model="selected"
                              size="sm"
                              :options="options"
                              name="radiosBtnDefault"
                              @change="selectChar">
          </b-form-radio-group>                    
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="fix-height">
      <b-col class="sidebar ml-3 p-0">
        <b-table  outlined 
                  hover 
                  :sort-by.sync="sortBy"
                  :items="items" 
                  :fields="fields" 
                  :filter="filter"
                  @row-clicked="myRowClickHandler">
          </b-table>
      </b-col>
      <b-col class="content">
        <books-list
              v-bind:key="selectedItem.id"
              v-bind:selectedItem="selectedItem">
        </books-list>
      </b-col>
    </b-row>
  </section>
</template>


<script>
import BooksList from "../books-list";
import store from "../../store";

//Массив фильтра авторов
var с_list = [];
//Массив авторов
var a_list = [];

export default {
  name: "tab-authors",
  components: {
    BooksList
  },
  data: function() {
    return {
      selected: "*",
      options: с_list,
      filter: null,
      selectedItem: "",
      info: null,
      loading: true,
      errored: false,
      fields: [
        {
          key: "author",
          label: "Авторы",
          sortable: true,
          tdClass: "control-element"
        },
        {
          key: "books",
          label: "Книг",
          sortable: true,
          thClass: "text-center",
          tdClass: "text-center control-element"
        }
      ],
      sortBy: "author",
      sortDesc: false,
      items: a_list
    };
  },
  mounted: function() {
    const self = this;
    //Вызов функции из глобального миксина
    this.callApi(
      this.$store.getters.prefix + "/static/api.php",
      { cmd: "с_list", dat: "" },
      "",
      function(rd) {
        с_list = rd; //возвр. данные (Responce)
        self.options = с_list;
      }
    );
    this.callApi(
      this.$store.getters.prefix + "/static/api.php",
      { cmd: "a_list", dat: "" },
      "",
      function(rd) {
        a_list = rd; //возвр. данные (Responce)
        self.items = a_list;
      }
    );
  },
  methods: {
    myRowClickHandler(item) {
      //сбросим атрибуты по всему массиву
      a_list.forEach(function(entry) {
        entry._rowVariant = "";
        entry.isActive = false;
      });
      item._rowVariant = "active";
      item.isActive = true;
      this.selectedItem = item;
    },
    selectChar(arg) {
      if (arg === "*") {
        this.filter = null;
      } else {
        var expr = new RegExp("(?:^|\\s)([" + arg + "][.]*)", "gi");
        this.filter = expr;
      }
      a_list.forEach(function(entry) {
        entry._rowVariant = "";
        entry.isActive = false;
      });
      this.selectedItem = "null";
    }
  }
};
</script>

<style lang='scss'>
</style>