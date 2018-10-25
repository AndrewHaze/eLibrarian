<template>
  <section>
    <loading v-if="loading" />
    <div v-if="isAuthenticated">
      <b-modal v-model="congShow" no-close-on-backdrop ok-only :title="appTitle">
        <div class="d-block fileName-center">
          <h3>Поздравляем! Вы успешно зарегистрировались!</h3>
        </div>
      </b-modal>
      <b-modal id="bookScanner" no-close-on-backdrop hide-footer size="lg" title="Импорт книг">
        <b-container fluid>
          <b-row class="mb-2">
            <b-col>
              <b-button-toolbar key-nav aria-label="Toolbar with button groups">
                <b-button-group class="e-btn-group mr-2">
                  <b-button variant="primary">Добавить папку</b-button>
                  <b-button variant="success" @click="openFiles">Добавить файлы</b-button>
                </b-button-group>
                <b-button variant="danger">Запуск</b-button>
                <input id="fi1" type="file" multiple @change="handleFileChange" />
              </b-button-toolbar>
            </b-col>
          </b-row>
          <b-row>
            <b-col>
              <b-form-group>
                <b-form-checkbox v-model="allSelected" :indeterminate="indeterminate" aria-describedby="files" aria-controls="files" @change="toggleAll" :title="allSelected ? 'Снять всё' : 'Выбрать всё'">
                </b-form-checkbox>
                <b-form-checkbox-group id="fls" stacked v-model="selected" :options=listInputFiles name="fls" aria-label="Individual files">
                </b-form-checkbox-group>
              </b-form-group>
            </b-col>
            <!---->
            <b-col>
  
            </b-col>
          </b-row>
        </b-container>
      </b-modal>
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
  
  .content {
    height: 100%;
    overflow-y: auto;
  }
  
  .control-element {
    cursor: pointer;
    user-select: none;
  }
  
  .e-btn-group {
    &>.btn+.btn {
      margin-left: 1px;
    }
  }
  
  .btn-toolbar>input[type="file"] {
    display: none;
  }
  
  #fls .custom-control-label>span {
    margin-left: 20px;
    &:before {
      content: url("../../assets/raw.png");
      position: absolute;
      left: -3px;
      top: 1px;
    }
  }
  
  #fls .custom-checkbox:nth-child(1) .custom-control-label>span {
    &:before {
      content: url("../../assets/add.png");
    }
  }
</style>

<script>
  import {
    mapGetters
  } from "vuex";
  import store from "../../store";
  import Login from "../login";
  import AuthorsTab from "../tab-authors";
  import GenresTab from "../tab-genres";
  import SeriesTab from "../tab-series";
  
  export default {
    name: "home",
    components: {
      Login,
      AuthorsTab,
      GenresTab,
      SeriesTab
    },
    data() {
      return {
        congShow: false,
        listInputFiles: [],
        buf: [],
        selected: [],
        allSelected: false,
        indeterminate: false
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
    },
    methods: {
      openFiles: function(e) {
        document.getElementById("fi1").click();
      },
      handleFileChange: function(e) {
        let filesList = e.target.files || e.dataTransfer.files;
        if (!filesList.length)
          return;
        for (let i = 0; i < filesList.length; i++) {
          // получаем сам файл
          this.listInputFiles.push({
            text: filesList[i].name,
            value: filesList[i].name,
            status: "add"
          });
        }
      },
      toggleAll(checked) {
        this.buf = this.multi2one(this.listInputFiles);
        this.selected = checked ? this.buf.slice() : [];
        this.buf = [];
      },
      multi2one(arr) {
        let newArr = [];
        for (let i = 0; i < arr.length; i++) {
          newArr[i] = arr[i].value;
        }
        return newArr;
      },
      find(array, value) {
        return array.indexOf(value);
      }
    },
    watch: {
      selected(newVal, oldVal) {
        // Handle changes in individual flavour checkboxes
        if (newVal.length === 0) {
          this.indeterminate = false;
          this.allSelected = false;
        } else if (newVal.length === this.files.length) {
          this.indeterminate = false;
          this.allSelected = true;
        } else {
          this.indeterminate = true;
          this.allSelected = false;
        }
      },
      listInputFiles(val) {
        for (let i = 0; i < val.length; i++) {
          let st = val[i].status;
          switch (st) {
            case 'add': // if (x === 'value1')
                break
            case 'value2': // if (x === 'value2')
                break
          }
        }
      }
    }
  };
</script>

