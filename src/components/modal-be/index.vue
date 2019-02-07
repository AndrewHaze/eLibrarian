<template>
  <b-modal
    id="bookEditor"
    ref="modal-e"
    @shown
    @hidden
    no-close-on-backdrop
    size="max"
    title="Книга"
    ok-title="Сохранить"
    cancel-title="Отмена"
  >
    <b-container fluid class="be-modal-content">
      <b-form-row>
        <b-col sm="3" class="cover-wrap">
          <b-img thumbnail fluid :src="fileName" alt="Обложка"/>
          <b-button variant="success" block size="sm" @click="openFiles" class="mt-2">Загрузить</b-button>
          <input id="cover-loader" type="file" @change="handleFileChange" accept=".jpg, .png">
        </b-col>
        <b-col sm="9" :style="{ maxHeight: mHeight + 'px', minHeight: mHeight + 'px' }">
          <b-container fluid class="px-0">
            <!-- ###################################### colappse 1 ###################################-->
            <div class="colappse-header">
              <span>Книга</span>
              <b-btn
                @click="showCollapse1 = !showCollapse1"
                :class="showCollapse1 ? 'collapsed' : null"
                aria-controls="collapse1"
                :aria-expanded="showCollapse1 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse1"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse1" class="collapseWrap" v-model="showCollapse1">
              <b-form-group
                id="collapse1InputGroup1"
                label="Название:"
                label-for="collapse1Input1"
                label-size="sm"
              >
                <b-form-input
                  id="collapse1Input1"
                  type="text"
                  v-model="form.bk_title"
                  required
                  placeholder="Введите название книги"
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse1InputGroup2"
                label="Оригинальное название:"
                label-for="collapse1Input2"
                label-size="sm"
              >
                <b-form-input
                  id="collapse1Input2"
                  type="text"
                  v-model="form.bk_original_title"
                  placeholder="Введите оригинальное название книги"
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse1InputGroup3"
                label="Авторы:"
                label-for="collapse1Select1"
                label-size="sm"
              >
                <v-select
                  id="collapse1Select1"
                  multiple
                  v-model="form.bk_authors"
                  :options="s2OptionsAuthors"
                >
                  <span slot="no-options">Совпадений нет</span>
                </v-select>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse1InputGroup4"
                label="Жанры:"
                label-for="collapse1Select2"
                label-size="sm"
              >
                <v-select id="collapse1Select2" multiple v-model="form.bk_genres" :options="s2OptionsGenres">
                  <span slot="no-options">Совпадений нет</span>
                </v-select>
              </b-form-group>
            </b-collapse>
            <!-- ###################################### colappse 2 ###################################-->
            <div class="colappse-header">
              <span>Серии</span>
              <b-btn
                @click="showCollapse2 = !showCollapse2"
                :class="showCollapse2 ? 'collapsed' : null"
                aria-controls="collapse2"
                :aria-expanded="showCollapse2 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse2"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse2" class="collapseWrap" v-model="showCollapse2">
              <b-row>
                <b-col>
                  <b-form-group
                    id="collapse2InputGroup1"
                    label="Серия:"
                    label-for="collapse2Select1"
                    label-size="sm"
                  >
                    <v-select id="collapse2Select1" v-model="form.bk_seriesTitle" :options="s2OptionsSeries">
                      <span slot="no-options">Совпадений нет</span>
                    </v-select>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="2">
                  <b-form-group
                    id="collapse2InputGroup2"
                    label="Номер:"
                    label-for="collapse2Input2"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse2Input2"
                      type="number"
                      v-model="form.bk_seriesNumber"
                      placeholder="№ серии"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col class="col-fix-2btn">
                  <b-button-group class="ad-btn-group">
                    <b-button variant="danger">
                      <font-awesome-icon icon="minus"/>
                    </b-button>
                    <b-button variant="success">
                      <font-awesome-icon icon="plus"/>
                    </b-button>
                  </b-button-group>
                </b-col>
              </b-row>
            </b-collapse>
            <!-- ###################################### colappse 3 ###################################-->
            <div class="colappse-header">
              <span>Дополнительно</span>
              <b-btn
                @click="showCollapse3 = !showCollapse3"
                :class="showCollapse3 ? 'collapsed' : null"
                aria-controls="collapse3"
                :aria-expanded="showCollapse3 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse3"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse3" class="collapseWrap" v-model="showCollapse3">
              <b-row>
                <b-col sm="4">
                  <b-form-group
                    id="collapse3InputGroup1"
                    label="Дата:"
                    label-for="collapse3Input1"
                    label-size="sm"
                  >
                    <b-form-input id="collapse3Input1" type="date" v-model="form.bk_date" size="sm"></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="4">
                  <b-form-group
                    id="collapse3InputGroup2"
                    label="Язык:"
                    label-for="collapse3Input2"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse3Input2"
                      type="text"
                      v-model="form.bk_language"
                      required
                      placeholder="Введите язык"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="4">
                  <b-form-group
                    id="collapse3InputGroup3"
                    label="Язык оригинала:"
                    label-for="collapse3Input3"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse3Input3"
                      type="text"
                      v-model="form.bk_orig_language"
                      required
                      placeholder="Введите язык оригинала"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <!-- Разделитель -->
              <b-form-group
                id="collapse3InputGroup4"
                label="Ключевые слова:"
                label-for="collapse3Input4"
                label-size="sm"
              >
                <b-form-input
                  id="collapse3Input4"
                  type="text"
                  v-model="form.bk_keywords"
                  placeholder="Введите ключевые слова"
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse3InputGroup5"
                label="Переводчик:"
                label-for="collapse3Input5"
                label-size="sm"
              >
                <b-form-input
                  id="collapse3Input5"
                  type="text"
                  v-model="form.bk_translator"
                  placeholder="Введите переводчика"
                  size="sm"
                ></b-form-input>
              </b-form-group>
            </b-collapse>
            <!-- ###################################### colappse 4 ###################################-->
            <div class="colappse-header">
              <span>Аннотация</span>
              <b-btn
                @click="showCollapse4 = !showCollapse4"
                :class="showCollapse4 ? 'collapsed' : null"
                aria-controls="collapse4"
                :aria-expanded="showCollapse4 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse4"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse4" class="collapseWrap" v-model="showCollapse4">
              <b-row>
                <b-col>
                  <b-form-group
                    id="collapse4InputGroup1"
                    label="Аннотация:"
                    label-for="collapse4Input1"
                    label-size="sm"
                  >
                    <b-form-textarea id="collapse4Textarea1" v-model="form.bk_annotation"></b-form-textarea>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-collapse>
            <!-- ###################################### colappse 5 ###################################-->
            <div class="colappse-header">
              <span>Информация о публикации</span>
              <b-btn
                @click="showCollapse5 = !showCollapse5"
                :class="showCollapse5 ? 'collapsed' : null"
                aria-controls="collapse5"
                :aria-expanded="showCollapse5 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse5"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse5" class="collapseWrap" v-model="showCollapse5">
              <b-form-group
                id="collapse5InputGroup1"
                label="Название публикации:"
                label-for="collapse5Input1"
                label-size="sm"
              >
                <b-form-input
                  id="collapse5Input1"
                  type="text"
                  v-model="form.bk_pub_title"
                  placeholder="Введите название публикации"
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse5InputGroup2"
                label="Издатель:"
                label-for="collapse5Input2"
                label-size="sm"
              >
                <b-form-input
                  id="collapse5Input2"
                  type="text"
                  v-model="form.bk_publisher"
                  placeholder="Введите издателя"
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-row>
                <b-col sm="5">
                  <b-form-group
                    id="collapse5InputGroup4"
                    label="Город:"
                    label-for="collapse5Input4"
                    label-size="sm"
                  >
                    <b-form-input id="collapse5Input4" type="text" v-model="form.bk_city" size="sm"></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="2">
                  <b-form-group
                    id="collapse5InputGroup5"
                    label="Год издания:"
                    label-for="collapse5Input5"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse5Input5"
                      type="text"
                      v-model="form.bk_pub_year"
                      required
                      placeholder="Введите год издания"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="5">
                  <b-form-group
                    id="collapse5InputGroup6"
                    label="ISBN:"
                    label-for="collapse5Input6"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse5Input6"
                      type="text"
                      v-model="form.bk_isbn"
                      required
                      placeholder="Введите ISBN"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-collapse>
            <!-- ###################################### colappse 6 ###################################-->
            <div class="colappse-header">
              <span>Информация о документе</span>
              <b-btn
                @click="showCollapse6 = !showCollapse6"
                :class="showCollapse6 ? 'collapsed' : null"
                aria-controls="collapse6"
                :aria-expanded="showCollapse6 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse6"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse6" class="collapseWrap" v-model="showCollapse6">
              <b-row>
                <b-col sm="6">
                  <b-form-group
                    id="collapse6InputGroup1"
                    label="ID документа:"
                    label-for="collapse6Input1"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse6Input1"
                      type="text"
                      v-model="form.bk_doc_id"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="4">
                  <b-form-group
                    id="collapse6InputGroup2"
                    label="Дата:"
                    label-for="collapse6Input2"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse6Input2"
                      type="date"
                      v-model="form.bk_doc_date"
                      required
                      placeholder="Введите дату документа"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="2">
                  <b-form-group
                    id="collapse6InputGroup3"
                    label="Версия:"
                    label-for="collapse6Input3"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse6Input3"
                      type="text"
                      v-model="form.bk_doc_ver"
                      required
                      placeholder="Введите версию документа"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <!-- Разделитель -->
              <b-form-group
                id="collapse6InputGroup4"
                label="Авторы:"
                label-for="collapse6Input4"
                label-size="sm"
              >
                <b-form-input
                  id="collapse6Input4"
                  type="text"
                  v-model="form.bk_doc_authors"
                  required
                  placeholder
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse6InputGroup5"
                label="Программы:"
                label-for="collapse6Input5"
                label-size="sm"
              >
                <b-form-input
                  id="collapse6Input5"
                  type="text"
                  v-model="form.bk_programms"
                  required
                  placeholder
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse6InputGroup6"
                label="Исходный URL:"
                label-for="collapse6Input6"
                label-size="sm"
              >
                <b-form-input
                  id="collapse6Input6"
                  type="text"
                  v-model="form.bk_url"
                  required
                  placeholder
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse6InputGroup7"
                label="Автор OCR:"
                label-for="collapse6Input7"
                label-size="sm"
              >
                <b-form-input
                  id="collapse6Input7"
                  type="text"
                  v-model="form.bk_ocr_authors"
                  required
                  placeholder
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-row>
                <b-col>
                  <b-form-group
                    id="collapse6InputGroup8"
                    label="История:"
                    label-for="collapse6Input8"
                    label-size="sm"
                  >
                    <b-form-textarea id="collapse6Textarea8" v-model="form.bk_ver_history"></b-form-textarea>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-collapse>
            <!-- ###################################### colappse 7 ###################################-->
            <div class="colappse-header">
              <span>Дополнительно</span>
              <b-btn
                @click="showCollapse7 = !showCollapse7"
                :class="showCollapse7 ? 'collapsed' : null"
                aria-controls="collapse7"
                :aria-expanded="showCollapse7 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse7"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse7" class="collapseWrap" v-model="showCollapse7">
              <b-row>
                <b-col sm="4">
                  <b-form-group
                    id="collapse7InputGroup1"
                    label="Дата файла:"
                    label-for="collapse7Input1"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse7Input1"
                      type="date"
                      v-model="form.bk_file_date"
                      size="sm"
                      readonly
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="4">
                  <b-form-group
                    id="collapse7InputGroup2"
                    label="Размер файла:"
                    label-for="collapse7Input2"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse7Input2"
                      type="text"
                      v-model="form.bk_file_size"
                      size="sm"
                      readonly
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="4">
                  <b-form-group
                    id="collapse7InputGroup3"
                    label="Формат:"
                    label-for="collapse7Input3"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse7Input3"
                      type="text"
                      v-model="form.bk_format"
                      size="sm"
                      readonly
                    ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <!-- Разделитель -->
              <b-form-group
                id="collapse7InputGroup4"
                label="Имя файла:"
                label-for="collapse7Input4"
                label-size="sm"
              >
                <b-form-input
                  id="collapse7Input4"
                  type="text"
                  v-model="form.bk_file_name"
                  size="sm"
                ></b-form-input>
              </b-form-group>
            </b-collapse>
          </b-container>
        </b-col>
      </b-form-row>
    </b-container>
  </b-modal>
</template>

<style lang="scss">
$line-color: #dee2e6;
$header-font-color: #495057;
$selected-color: #ddd;
$hover-color: rgba(221, 221, 221, 0.4);
$header-bk-color: #abafb4;

.be-modal-content {
  overflow-x: hidden;
  overflow-y: scroll;
}

input[type="file"] {
  display: none;
}

.cover-wrap {
  text-align: center;
  padding-right: 1.5rem !important;
  .img-thumbnail {
    width: 100%;
  }
}

.colappse-header {
  display: flex;
  flex-flow: row nowrap;
  justify-content: space-between;
  position: relative;
  font-weight: 600;
  &::before {
    position: absolute;
    content: "";
    border-bottom: 1px solid #737b83;
    left: 0;
    right: 1.6rem;
    top: 0.95rem;
  }
  span {
    padding-right: 0.3rem;
    background-color: #fff;
    z-index: 1;
  }
  .btn-circle {
    width: 20px;
    height: 20px;
    text-align: center;
    padding: 4px 0;
    font-size: 12px;
    line-height: 0;
    border-radius: 10px;
    //border-color: #212529;
    margin-top: 5px;
    &:focus {
      box-shadow: none;
    }
  }
}
.collapseWrap {
  padding: 0.5rem 1rem 0;
  .col-fix-2btn {
    display: flex;
    flex-flow: column;
    justify-content: end;
    max-width: 5.65rem;
    padding-bottom: .1rem;
    .ad-btn-group {
      max-height: 32px;
      margin-bottom: 15px;
      .btn {
        padding: 0.2rem 0.5rem !important;
        font-size: 0.9rem;
        &:focus {
          box-shadow: none;
        }
      }
    }
  }
}

.v-select {
  font-size: 0.875rem;
  .form-control {
    height: auto;
  }
  .dropdown-toggle::after {
    border-top: 0.3em solid transparent;
    border-right: none;
    margin-left: 0;
  }
  .dropdown-menu li a {
    padding: 0.1rem 0.5rem !important;
    font-size: 0.875rem;
  }
}

.open .dropdown-toggle {
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>

<script>
import axios from "axios";
import store from "../../store";
import vSelect from 'vue-select';

const shiftL = 225;
const shiftR = 6;

export default {
  name: "modal-be",
  components: {
    vSelect
  },
  data() {
    return {
      mHeight: 100,
      pHeight: 100,
      showCollapse1: true,
      showCollapse2: true,
      showCollapse3: true,
      showCollapse4: true,
      showCollapse5: true,
      showCollapse6: true,
      showCollapse7: true,
      fileName: "https://picsum.photos/250/250/?image=59",
      form: {
        bk_title: "",
        bk_original_title: "",
        bk_authors: null,
        bk_genres: null,
        bk_seriesTitle: null,
        bk_seriesNumber: "",
        bk_date: "",
        bk_language: "",
        bk_orig_language: "",
        bk_keywords: "",
        bk_translator: "",
        bk_annotation: "",
        bk_pub_title: "",
        bk_publisher: "",
        bk_city: "",
        bk_pub_year: "",
        bk_isbn: "",
        bk_doc_id: "",
        bk_doc_date: "",
        bk_doc_ver: "",
        bk_doc_authors: "",
        bk_programms: "",
        bk_url: "",
        bk_ocr_authors: "",
        bk_file_date: "",
        bk_file_size: "",
        bk_format: "",
        bk_file_name: ""
      },
      s2OptionsAuthors: ["op1", "op2", "op3"],
      s2OptionsGenres: ["op1", "op2", "op3"],
      s2OptionsSeries: ["op1", "op2", "op3"]
    };
  },
  mounted: function() {
    window.addEventListener("resize", this.handleResize);
    this.mHeight = window.innerHeight - shiftL;
    if (this.mHeight > 1200) {
      this.mHeight = 1200;
    }
  },
  beforeDestroy: function() {
    window.removeEventListener("resize", this.handleResize);
  },
  methods: {
    handleResize() {
      this.mHeight = window.innerHeight - shiftL;
      if (this.mHeight > 1200) {
        this.mHeight = 1200;
      }
      this.pHeight = this.mHeight / 2 - shiftR;
    },
    openFiles() {
      document.getElementById("cover-loader").click();
    },
    handleFileChange(e) {
      let filesList = e.target.files;
      if (!filesList.length) return;
      console.log(filesList.length);
      // let formData = new FormData();
      // formData.append("file", filesList[0]);
      for (let i = 0; i < filesList.length; i++) {
        var reader = new FileReader();
        // Closure to capture the file information.
        reader.onload = (function(theFile) {
          return function(e) {
            // Render thumbnail.
            this.fileName = e.target.result;
          };
        })(filesList[i]);
        // Read in the image file as a data URL.
        reader.readAsDataURL(filesList[i]);
      }
    }
  }
};
</script>
