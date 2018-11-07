<template>
  <section>
    <loading v-if="loading" />
    <div v-if="isAuthenticated">
      <b-modal v-model="congShow" no-close-on-backdrop ok-only :title="appTitle">
        <div class="d-block fileName-center">
          <h3>Поздравляем! Вы успешно зарегистрировались!</h3>
        </div>
      </b-modal>
      <b-modal id="bookScanner" @shown="showBookScanner" no-close-on-backdrop hide-footer size="lg" title="Импорт книг">
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
                  <b-form-checkbox v-model="allSelected" :disabled="!countLIF" :indeterminate="indeterminate" aria-describedby="listInputFiles" aria-controls="listInputFiles" @change="toggleAll" :title="allSelected ? 'Снять всё' : 'Выбрать всё'">
                  </b-form-checkbox>
                  <b-img :src="require('../../assets/info.png')" height="16" />
                  <div class="list-header-body" @click="sortListInputFiles">Имя файла
                    <span class="list-header-sort-desc" :class="{ active: desc }">&#8593;</span>
                    <span class="list-header-sort-asc" :class="{ active: asc }">&#8595;</span>
                  </div>
                </div>
                <div v-if="fCount > 0" class="animation-wrap">
                  <div class="lds-ring">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                  </div>
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

#bookScanner * {
  user-select: none;
  .custom-control,
  .custom-control-label,
  .custom-control-input {
    cursor: pointer;
  }
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

#fls .err span {
  color: red;
  &:before {
    content: url("../../assets/err.png");
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
  .list-header-body {
    display: flex;
    flex: 1 1 auto;
    position: relative;
    margin-left: 0.5rem;
    font-weight: bold;
    cursor: pointer;
  }
  .list-header-sort-desc,
  .list-header-sort-asc {
    position: absolute;
    margin: 0;
    padding: 0;
    opacity: 0.4;
    top: 0;
  }
  .active {
    opacity: 1;
  }
  .list-header-sort-desc {
    right: 8px;
  }
  .list-header-sort-asc {
    right: 0px;
  }
}

/* Animation */

$ring_color: #cce5ff;
$width: 15rem;
$height: 15rem;
.animation-wrap {
  position: absolute;
  width: $width;
  height: $height;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
}

.lds-ring {
  display: inline-block;
  position: relative;
  width: $width;
  height: $height;
}

.lds-ring div {
  box-sizing: border-box;
  display: block;
  position: absolute;
  width: $width/1.25;
  height: $height/1.25;
  margin: 16px;
  border: 16px solid $ring_color;
  border-radius: 50%;
  animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  border-color: $ring_color transparent transparent transparent;
}

.lds-ring div:nth-child(1) {
  animation-delay: -0.45s;
}

.lds-ring div:nth-child(2) {
  animation-delay: -0.3s;
}

.lds-ring div:nth-child(3) {
  animation-delay: -0.15s;
}

@keyframes lds-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
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
import axios from "axios";

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
      asc: false,
      desc: false,
      fCount: -1
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
    },
    countLIF: function() {
      return this.listInputFiles.length;
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
      this.fCount = filesList.length;
      for (let i = 0; i < filesList.length; i++) {
        const self = this;
        let formData = new FormData();
        formData.append("file", filesList[i]);

        axios({
          method: "post",
          url: this.$store.getters.prefix + "/static/upload.php",
          data: formData,
          withCredentials: true,  //передаем куки
          headers: {
            "content-type": "application/x-www-form-urlencoded"
          }
        })
          .then(response => {
            let rd = response.data;
            if (rd.success) {
              self.listInputFiles.push({
                text: rd.data.base_name,
                disabled: false,
                value: rd.data.hash_name,
                status: rd.data.status
              });
              self.fCount--;
            } else {
              self.listInputFiles.push({
                text: filesList[i].name,
                disabled: true,
                value: Math.random()
                  .toString(36)
                  .substr(2, 9),
                status: "err"
              });
              self.fCount--;
            }
          })
          .catch(error => {
            self.listInputFiles.push({
              text: filesList[i].name,
              disabled: true,
              value: Math.random()
                .toString(36)
                .substr(2, 9),
              status: "err"
            });
            self.fCount--;
          });
      }
    },
    toggleAll(checked) {
      this.buf = this.multi2one(this.listInputFiles);
      this.selected = checked ? this.buf.slice() : [];
    },
    showBookScanner(evt) {
      this.listInputFiles = [];
      this.fCount = -1;
      this.asc = false;
      this.desc = false;
      //Вызов функции из глобального миксина
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "clear_upload", //очищаем загрузку при закрытии окна
          dat: ""
        },
        "",
        function(rd) {}
      );
    },
    multi2one(arr) {
      let newArr = [];
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].status != "err") newArr.push(arr[i].value);
      }
      return newArr;
    },
    removeClasses(obj) {
      //удаляем классы по списку из array
      let array = ["add", "raw", "err"];
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
    },
    sortListInputFiles() {
      if (this.countLIF < 1) return;

      function sortASC(a, b) {
        let x = a.text.toLowerCase();
        let y = b.text.toLowerCase();
        return x < y ? -1 : x > y ? 1 : 0;
      }

      function sortDESC(a, b) {
        let x = a.text.toLowerCase();
        let y = b.text.toLowerCase();
        return x > y ? -1 : x < y ? 1 : 0;
      }

      if (this.fCount === 0) {
        this.asc = false;
        this.desc = false;
        this.fCount = -1;
      }

      if (this.asc) {
        this.desc = true;
        this.asc = false;
      } else if (this.desc) {
        this.desc = false;
        this.asc = true;
      } else {
        this.desc = false;
        this.asc = true;
      }
      if (this.asc) this.listInputFiles.sort(sortASC);
      else this.listInputFiles.sort(sortDESC);
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
        //выжидаем мгновение, иначе не подхватываютя изменения
        let c = document.getElementById("fls").children;
        for (let i = 0; i < val.length; i++) {
          this.removeClasses(c[i]);
          let st = val[i].status;
          switch (st) {
            case "add":
              c[i].classList.add("add");
              c[i].setAttribute("title", "Файл готов к обработке");
              break;
            case "raw":
              c[i].classList.add("raw");
              c[i].setAttribute("title", "Файл успешно добавлен");
              break;
            case "err":
              c[i].classList.add("err");
              c[i].setAttribute("title", "Ошибка чтения");
              break;
          }
        }
      }, 0);
    },
    fCount(val) {
      if (val === 0) {
        this.sortListInputFiles();
      }
    }
  }
};
</script>

