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
                  :items="items" 
                  :fields="fields" 
                  :filter="filter"
                  @row-clicked="myRowClickHandler">
          </b-table>
      </b-col>
      <b-col class="content">
        <BooksList
              v-bind:key="selectedItem.id"
              v-bind:selectedItem="selectedItem">
        </BooksList>
      </b-col>
    </b-row>
  </section>
</template>


<script>
import axios from "axios";
import BooksList from "./BooksList.vue";

const instance = axios.create({
  responseType: "json",
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
    Authorization: "test",
    "X-Test": "testing"
  }
});

const str = JSON.stringify("1234");

var list = [
  {
    id: 1,
    books: 1,
    author: "Акунин",
    isActive: false,
    _rowVariant: ""
  },
  {
    id: 2,
    books: 2,
    author: "Бушков",
    isActive: false,
    _rowVariant: ""
  },
  {
    id: 3,
    books: 3,
    author: "Пехов",
    isActive: false,
    _rowVariant: ""
  },
  {
    id: 4,
    books: 0,
    author: "AdamПs",
    isActive: false,
    _rowVariant: ""
  }
];

export default {
  components: {
    BooksList
  },
  data: function() {
    return {
      selected: "*",
      options: [
        { text: "*", value: "*" },
        { text: "А", value: "А" },
        { text: "Б", value: "Б" },
        { text: "В", value: "В" }
      ],
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
      items: list
    };
  },
  mounted() {
    axios
      .post("/static/test.php", { str: "1234" })
      .then(response => {
        list = response.data;
        this.items = list;
      })
      .catch(error => {
        console.log(error);
        this.errored = true;
      })
      .finally(() => (this.loading = false));
  },
  methods: {
    myRowClickHandler(item) {
      //сбросим атрибуты по всему массиву
      list.forEach(function(entry) {
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
      list.forEach(function(entry) {
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