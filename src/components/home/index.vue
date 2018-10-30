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
          <b-row class="mb-3">
            <b-col>
              <b-button-toolbar key-nav aria-label="Toolbar with button groups">
                <b-button variant="success" @click="openFiles" class="mr-2">Добавить файлы</b-button>
                <b-button variant="danger" :disabled="buttonStartProc" @click="startProc">Запуск</b-button>
                <input id="fi1" type="file" multiple @change="handleFileChange" />
              </b-button-toolbar>
            </b-col>
          </b-row>
          <b-row>
            <b-col>
              <b-form-group>
                <div class="list-header">
                  <b-form-checkbox v-model="allSelected" :indeterminate="indeterminate" aria-describedby="listInputFiles" aria-controls="listInputFiles" @change="toggleAll" :title="allSelected ? 'Снять всё' : 'Выбрать всё'">
                  </b-form-checkbox>
                  <b-img :src="require('../../assets/info.png')" height="16" />
                  <span>Имя файла</span>
                </div>
                <b-form-checkbox-group id="fls" class="f-list" :style="{ maxHeight: mHeight + 'px', minHeight: mHeight + 'px' }" stacked v-model="selected" :options=listInputFiles name="fls" aria-label="Individual files">
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
  & > .btn + .btn {
    margin-left: 0.12rem;
  }
}

.btn-toolbar > input[type="file"] {
  display: none;
}

#fls .custom-control-label > span {
  margin-left: 1.65rem;
  &:before {
    content: "";
    position: absolute;
    left: 0;
    top: 0.12rem;
  }
}

#fls .raw span {
  &:before {
    content: url("../../assets/raw.png");
  }
}

#fls .add span {
  &:before {
    content: url("../../assets/add.png");
  }
}

.f-list {
  padding: 0.25rem 0.5rem;
  border: 1px solid #dee2e6;
  overflow: auto;
}

.list-header {
  display: flex;
  align-items: center;
  border: 1px solid #dee2e6;
  padding: 0.2rem 0.5rem;
  .custom-control-inline {
    margin-right: 0.1rem;
  }
  span {
    margin-left: 0.5rem;
    font-weight: bold;
  }
}
</style>

<script>
import { mapGetters } from "vuex";
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
      listInputFiles: [], //экранное представление
      buf: [],
      selected: [],
      allSelected: false,
      indeterminate: false,
      buttonStartProc: true,
      mHeight: 100,
      img: ""
    };
  },
  created: function() {
    if (this.congratulation) {
      this.congShow = true;
    }
  },
  mounted: function() {
    window.addEventListener("resize", this.handleResize);
    this.mHeight = window.innerHeight - 260;
  },
  beforeDestroy: function() {
    window.removeEventListener("resize", this.handleResize);
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
    handleResize() {
      this.mHeight = window.innerHeight - 260;
    },
    openFiles(e) {
      document.getElementById("fi1").click();
    },
    handleFileChange(e) {
      let filesList = e.target.files || e.dataTransfer.files;
      if (!filesList.length) return;
      for (let i = 0; i < filesList.length; i++) {
        // получаем сам файл
        this.listInputFiles.push({
          text: filesList[i].name,
          value: filesList[i].name,
          //url: window.URL.createObjectURL(filesList[i]),
          status: "raw"
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
    removeClasses(obj) {
      //удаляем классы по списку из array
      let array = ["add", "raw"];
      let clsList = obj.className.split(" "); //Получаем массив классов
      let result = [];
      for (let i = 0; i < clsList.length; i++) {
        if (array.indexOf(clsList[i]) === -1) result.push(clsList[i]);
      }
      obj.className = result.join(" ");
    },
    startProc() {
      this.buf = this.listInputFiles;
      //иначе не отслеживает изменения
      this.listInputFiles = [];
      for (let i = 0; i < this.buf.length; i++) {
        let idx = this.selected.indexOf(this.buf[i].value);
        if (idx != -1) {
          this.selected.splice(idx, 1);
          this.buf[i].status = "add";
          /* exec */
        }
      }
      this.listInputFiles = this.buf;
    }
  },
  watch: {
    selected(newVal, oldVal) {
      // Handle changes in individual flavour checkboxes
      if (newVal.length === 0) {
        this.indeterminate = false;
        this.allSelected = false;
        this.buttonStartProc = true;
      } else if (newVal.length === this.listInputFiles.length) {
        this.indeterminate = false;
        this.allSelected = true;
        if (this.listInputFiles.length > 0) {
          this.buttonStartProc = false;
        }
      } else {
        this.indeterminate = true;
        this.allSelected = false;
        this.buttonStartProc = false;
      }
    },
    listInputFiles(val) {
      setTimeout(() => {
        let c = document.getElementById("fls").children;
        for (let i = 0; i < val.length; i++) {
          this.removeClasses(c[i]);
          let st = val[i].status;
          switch (st) {
            case "add":
              c[i].classList.add("add");
              break;
            case "raw":
              c[i].classList.add("raw");
              break;
          }
        }
      }, 0);
    }
  }
};
</script>

