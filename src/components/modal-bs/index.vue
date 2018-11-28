<template>
<b-modal id="bookScanner" @shown="showBookScanner" @hidden="bookScannerHidden" no-close-on-backdrop hide-footer size="max" title="Импорт книг">
    <b-container fluid>
        <b-row class="mb-2">
            <b-col>
                <b-button-toolbar key-nav aria-label="Toolbar with button groups">
                    <b-button variant="success" @click="openFiles" class="mr-2">Добавить файлы</b-button>
                    <b-button variant="danger" :disabled="buttonStartProc" @click="startProc">Запуск</b-button>
                    <input id="fi1" type="file" multiple @change="handleFileChange" accept=".fb2,.zip"/>
          </b-button-toolbar>
            </b-col>
        </b-row>
        <b-row>
            <b-col class="col-5">
                <div class="hm">Добавленные файлы</div>
                <b-form-group>
                    <div class="list-header">
                        <b-form-checkbox v-model="allSelected" :disabled="!countLIF" :indeterminate="indeterminate" aria-describedby="listInputFiles" aria-controls="listInputFiles" @change="toggleAll" :title="allSelected ? 'Снять всё' : 'Выбрать всё'">
                        </b-form-checkbox>
                        <b-img :src="require('../../assets/info.png')" />
                        <div class="list-header-body" @click="sortListInputFiles">Имя файла
                            <span class="list-header-sort-desc" :class="{ active: iDesc }">&#8593;</span>
                            <span class="list-header-sort-asc" :class="{ active: iAsc }">&#8595;</span>
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
            <b-col class="col-7 right-col">
                <div class="hm">Обработанные файлы</div>
                <div class="list-header">
                    <b-img :src="require('../../assets/info.png')" />
                    <div class="list-header-body" @click="sortListProcessingFiles">Имя файла
                        <span class="list-header-sort-desc" :class="{ active: pDesc }">&#8593;</span>
                        <span class="list-header-sort-asc" :class="{ active: pAsc }">&#8595;</span>
                    </div>
                </div>
                <div class="f-list" :style="{ maxHeight: pHeight + 'px', minHeight: pHeight + 'px' }">
                    <ListItems v-for="listItem in listProcessingFiles" :listItem="listItem" :key="listItem.id" />
                </div>
                <div class="info-panel" :style="{ maxHeight: pHeight + 'px', minHeight: pHeight + 'px' }">
                    <div class="ip-legend">
                        <div class="hm">Обозначения</div>
                        <div class="ip-body">
                            <ul>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/raw.png'">Отмечен для обработки</li>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/add.png'">Успешно добавлен</li>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/add.png'">Обновлено</li>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/add.png'">Дубликат (идентичный)</li>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/add.png'">Дубликат (старее)</li>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/add.png'">Дубликат (ID отличается)</li>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/add.png'">Дубликат (Название отличается)</li>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/add.png'">Дубликат (новее)</li>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/per.png'">Ошибка разбора</li>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/dma.png'">Повреждённый архив</li>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/dbe.png'">Ошибка обновления БД</li>
                                <li><img :src="this.$store.getters.prefix + '/static/assets/ndf.png'">Требуется описание книги</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </b-col>
        </b-row>
    </b-container>
</b-modal>
</template>

<style lang="scss">
.e-btn-group {
  & > .btn + .btn {
    margin-left: 0.12rem;
  }
}

.btn-toolbar > input[type="file"] {
  display: none;
}

#bookScanner {
  user-select: none;

  .hm {
    font-size: 1.15rem;
    margin-bottom: 0.5rem;
    font-family: inherit;
    color: #1414fc;
    font-weight: 500;
    line-height: 1.2;
  }

  .list-element,
  .custom-control,
  .custom-control-label,
  .custom-control-input {
    cursor: pointer;
  }

  .f-list {
    padding: 0;
    border: 1px solid #dee2e6;
    overflow: auto;

    .custom-control {
      padding: 0.1rem 0.8rem 0.1rem 2rem;

      &:hover {
        background-color: #eee;
      }
    }

    .err:hover {
      background-color: transparent;
    }

    .custom-control + .custom-control {
      padding-top: 0;
    }

    .list-element,
    .custom-control-label {
      width: 100%;
      word-wrap: normal;
      white-space: nowrap;
    }
  }

  .right-col {
    display: flex;
    flex-flow: column nowrap;
    padding-left: 0;

    .f-list {
      margin-bottom: 12px;
    }

    .info-panel {
      display: flex;
      flex-flow: row nowrap;

      .ip-legend {
        display: flex;
        flex-flow: column nowrap;
        padding: 0;
      }

      .ip-body {
        overflow: auto;
        flex: 1 1 auto;
        padding: 0.2rem 1rem 0.4rem 0.5rem;
        border: 1px solid #dee2e6;

        & > ul {
          list-style-type: none;
          margin: 0;
          padding: 0;

          & > li {
            word-wrap: normal;
            white-space: nowrap;
            line-height: 1.6;

            & > img {
              margin-right: 6px;
              margin-top: -3px;
              border: 1px solid darkgray;
              border-radius: 2px;
              background-color: #f5f5fd;
            }
          }
        }
      }
    }
  }

  #fls .custom-control-label > span {
    margin-left: 1.8rem;

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

  #fls .dma span {
    &:before {
      content: url("../../assets/dma.png");
    }
  }

  #fls .ndf span {
    &:before {
      content: url("../../assets/ndf.png");
    }
  }

  #fls .per span {
    &:before {
      content: url("../../assets/per.png");
    }
  }

  #fls .dbe span {
    &:before {
      content: url("../../assets/dbe.png");
    }
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

  /////////
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
import axios from "axios";
import ListItems from "../modal-bs-items-list";

const shiftL = 280;
const shiftR = 6;

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

export default {
  name: "modal-bs",
  components: {
    ListItems
  },
  data() {
    return {
      /* левая колонка */
      listInputFiles: [], //экранное представление
      buf: [],
      selected: [],
      allSelected: false,
      indeterminate: false,
      buttonStartProc: true,
      mHeight: 100,
      pHeight: 100,
      iAsc: false,
      iDesc: false,
      fCount: -1, //счетчик добавляемых файлов
      /* правая колонка */
      listProcessingFiles: [],
      pAsc: false,
      pDesc: false,
      bCount: -1 //счетчик обработанных файлов
    };
  },
  mounted: function() {
    window.addEventListener("resize", this.handleResize);
    this.mHeight = window.innerHeight - shiftL;
    if (this.mHeight > 1200) {
      this.mHeight = 1200;
    }
    this.pHeight = this.mHeight / 2 - shiftR;
  },
  beforeDestroy: function() {
    window.removeEventListener("resize", this.handleResize);
  },
  computed: {
    countLIF: function() {
      return this.listInputFiles.length;
    },
    countLPF: function() {
      return this.listProcessingFiles.length;
    }
  },
  methods: {
    handleResize() {
      this.mHeight = window.innerHeight - shiftL;
      if (this.mHeight > 1200) {
        this.mHeight = 1200;
      }
      this.pHeight = this.mHeight / 2 - shiftR;
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
          withCredentials: true, //передаем куки
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
      this.listProcessingFiles = [];
      this.mbsItems = [];
      this.fCount = -1;
      this.bCount = -1;
      this.iAsc = false;
      this.iDesc = false;
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
    bookScannerHidden(evt) {
      this.$parent.updateAll();
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
      let array = ["add", "raw", "err", "dma", "ndf", "per", "dbe"];
      let clsList = obj.className.split(" "); //Получаем массив классов
      let result = [];
      for (let i = 0; i < clsList.length; i++) {
        if (array.indexOf(clsList[i]) === -1) result.push(clsList[i]);
      }
      obj.className = result.join(" ");
    },
    sortListInputFiles() {
      if (this.countLIF < 1) return;

      if (this.fCount === 0) {
        this.iAsc = false;
        this.iDesc = false;
        this.fCount = -1;
      }

      if (this.iAsc) {
        this.iDesc = true;
        this.iAsc = false;
      } else if (this.iDesc) {
        this.iDesc = false;
        this.iAsc = true;
      } else {
        this.iDesc = false;
        this.iAsc = true;
      }
      if (this.iAsc) this.listInputFiles.sort(sortASC);
      else this.listInputFiles.sort(sortDESC);
    },
    sortListProcessingFiles() {
      if (this.countLPF < 1) return;

      if (this.bCount === 0) {
        this.pAsc = false;
        this.pDesc = false;
        this.bCount = -1;
      }

      if (this.pAsc) {
        this.pDesc = true;
        this.pAsc = false;
      } else if (this.pDesc) {
        this.pDesc = false;
        this.pAsc = true;
      } else {
        this.pDesc = false;
        this.pAsc = true;
      }
      if (this.pAsc) this.listProcessingFiles.sort(sortASC);
      else this.listProcessingFiles.sort(sortDESC);
    },
    startProc() {
      this.buf = this.listInputFiles;
      this.bCount = this.selected.length; //количество отмеченных
      for (let i = 0; i < this.buf.length; i++) {
        let idx = this.selected.indexOf(this.buf[i].value);
        if (idx != -1) {
          //обрабатываем омеченные
          /* exec */
          const self = this;
          axios({
            method: "post",
            url: this.$store.getters.prefix + "/static/api.php",
            data: {
              cmd: "proc",
              file: self.buf[i].value
            },
            withCredentials: true, //передаем куки
            headers: {
              "content-type": "application/x-www-form-urlencoded"
            }
          })
            .then(response => {
              let rd = response.data;
              if (rd.success) {
                self.buf[i].status = "add";
                self.listProcessingFiles.push({
                  text: self.buf[i].text,
                  //text: rd.data.test,
                  value: rd.data.hash_name,
                  status: "add"
                });
                self.bCount--;
              } else {
                let errCode = rd.error;
                if (!errCode) {
                  errCode = "dbe";
                }
                self.buf[i].status = errCode;
                console.log("success:", rd.success, " error: ", rd.error);
                self.listProcessingFiles.push({
                  text: self.buf[i].text,
                  value: self.buf[i].value,
                  status: errCode
                });
                self.bCount--;
              }
            })
            .catch(error => {
              self.buf[i].status = rd.error;
              self.listProcessingFiles.push({
                text: self.buf[i].text,
                value: self.buf[i].value,
                status: rd.error
              });
              self.bCount--;
            });
        }
      }
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
              c[i].setAttribute("title", "Файл успешно добавлен");
              break;
            case "raw":
              c[i].classList.add("raw");
              c[i].setAttribute("title", "Файл готов к обработке");
              break;
            case "err":
              c[i].classList.add("err");
              c[i].setAttribute("title", "Ошибка чтения");
              break;
            case "ndf":
              c[i].classList.add("ndf");
              c[i].setAttribute("title", "Требуется описание книги");
              break;
            case "dma":
              c[i].classList.add("dma");
              c[i].setAttribute("title", "Поврежденный архив");
              break;
            case "per":
              c[i].classList.add("per");
              c[i].setAttribute("title", "Ошибка разбора");
              break;
            case "dbe":
              c[i].classList.add("dbe");
              c[i].setAttribute("title", "Ошибка обновления БД");
              break;
          }
        }
      }, 0);
    },
    fCount(val) {
      if (val === 0) {
        this.sortListInputFiles();
      }
    },
    bCount(val) {
      if (val === 0) {
        this.selected = [];
        this.listInputFiles = [];
        this.listInputFiles = this.buf;
        this.sortListProcessingFiles();
      }
    }
  }
};
</script>
