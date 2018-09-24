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
        <books-list
              v-bind:key="selectedItem.id"
              v-bind:selectedItem="selectedItem">
        </books-list>
      </b-col>
    </b-row>
  </section>
</template>


<script>
import axios from "axios";
import BooksList from "../books-list";

const dn = "l.mgr.loc";

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
  /*  {
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
  }*/
];

export default {
  name: "tab-authors",
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
  mounted: function() {
    const self = this;
    this.callApi("http://l.mgr.loc/static/api.php", "1234", function(rd) {
      list = rd;
      self.items = list;
    });
  },
  methods: {
    setServerError(m, d) {
      console.log("******* db_api call *******");
      console.log(m);
      console.log(d);
      console.log("***************************");
      return;
    },
    callApi(url, prms, callback) {
      axios({
        method: "post",
        url: url,
        data: prms
      })
        .then(response => {
          // в response.data получаем JSON,
          // в моем случае сервер формирует обязательные поля success,error,buffer
          // в buffer  перед выдачей JSON снимается html-вывод, возможно это отладочная информация,
          // которую выдает backend, возможно PHP-warnings
          let rspData = response.data;
          if (!rspData.success) {
            this.setServerError(rspData.error);
          } else {
            // ну и, собственно, сам вызов колбека, который происходит только в случае успешного приема данных
            this.setServerError("No errors", "No messages"); // это функция, которая в data выставляет определенные поля
            //в результате чего ошибки выводятся прямо на странице, удобно для отладки
            callback(rspData.data);
          }
        })
        .catch(error => {
          // эту часть вызывает сам axios при возникновении серверных ошибок, то есть все, что не 200 OK
          // позволяет увидеть, в частности, ошибку 500, вернее сам факт ее возникновения, если она обрабатывается
          // "стандартным" методом апача - пустая страница и все
          this.setServerError(error.message, error.stack);
          this.errored = true;
        })
        .finally(() => (this.loading = false));
    },
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