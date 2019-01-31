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
          <b-button variant="outline-secondary" size="sm" @click="openFiles" class="mt-2">Загрузить</b-button>
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
                label-for="collapse1Input3"
                label-size="sm"
              >
                <b-form-input
                  id="collapse1Input3"
                  type="text"
                  v-model="form.bk_authors"
                  required
                  placeholder="Введите авторов"
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse1InputGroup4"
                label="Жанры:"
                label-for="collapse1Input4"
                label-size="sm"
              >
                <b-form-input
                  id="collapse1Input4"
                  type="text"
                  v-model="form.bk_genres"
                  placeholder="Введите жанры"
                  size="sm"
                ></b-form-input>
              </b-form-group>
            </b-collapse>
            <!-- ###################################### colappse 2 ###################################-->
            <div class="colappse-header">
              <span>Серии</span>
              <b-btn
                @click="showCollapse2 = !showCollapse2"
                :class="showCollapse2 ? 'collapsed' : null"
                aria-controls="collapse1"
                :aria-expanded="showCollapse2 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse2"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse1" class="collapseWrap" v-model="showCollapse2">
              <b-row>
                <b-col>
                  <b-form-group
                    id="collapse2InputGroup1"
                    label="Серия:"
                    label-for="collapse2Input1"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse2Input1"
                      type="text"
                      v-model="form.bk_seriesTitle"
                      required
                      placeholder="Введите серию"
                      size="sm"
                    ></b-form-input>
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
                <b-col sm="1" class="d-flex flex-column justify-content-end">
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
            <b-collapse id="collapse1" class="collapseWrap" v-model="showCollapse3">
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
                id="collapse4InputGroup4"
                label="Ключевые слова:"
                label-for="collapse4Input4"
                label-size="sm"
              >
                <b-form-input
                  id="collapse4Input4"
                  type="text"
                  v-model="form.bk_keywords"
                  placeholder="Введите ключевые слова"
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
  overflow-y: auto;
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
</style>

<script>
import axios from "axios";
import store from "../../store";

const shiftL = 225;
const shiftR = 6;

export default {
  name: "modal-be",
  data() {
    return {
      mHeight: 100,
      pHeight: 100,
      showCollapse1: true,
      showCollapse2: true,
      showCollapse3: true,
      fileName: "https://picsum.photos/250/250/?image=59",
      form: {
        bk_title: "",
        bk_original_title: "",
        bk_authors: "",
        bk_genres: "",
        bk_seriesTitle: "",
        bk_seriesNumber: "",
        bk_date: "",
        bk_language: "",
        bk_orig_language: "",
        bk_keywords: "",
      }
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
