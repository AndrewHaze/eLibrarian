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

const prefix = "http://l.mgr.loc";

var list = [];

export default {
  name: "tab-authors",
  components: {
    BooksList
  },
  data: function() {
    return {
      selected: "*",
      options: [
        //Рыба фильтра авторов
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
    this.callApi(prefix + "/static/api.php", {cmd: 'a_list', dat: ""}, function(rd) {
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
          // сервер формирует обязательные поля data,success,error
          let rspData = response.data;
          if (!rspData.success) {
            this.setServerError(rspData.error, "");
          } else {
            callback(rspData.data);
          }
        })
        .catch(error => {
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