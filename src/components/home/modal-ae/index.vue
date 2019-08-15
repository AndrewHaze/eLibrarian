<template>
  <b-modal
    id="authorEditor"
    ref="modal-a"
    @show="showAuthorEditor"
    @shown="shownAuthorEditor"
    @hidden="closeAuthorEditor"
    no-close-on-backdrop
    size="xl"
    title="Автор"
    ok-title="Сохранить"
    cancel-title="Отмена"
  >
    <b-container
      :id="'author'+atID"
      fluid
      class="ae-modal-content"
      :style="{ maxHeight: mHeight + 'px' }"
    >
      <b-form-row>
        <b-col sm="3" class="cover-wrap">
          <div class="cover-wrap-sticky">
            <div id="photo-drop-area">
              <b-img thumbnail fluid :src="photoImage" />
            </div>
            <div>
              <b-button variant="success" block size="sm" @click="openFiles" class="mt-2">Загрузить</b-button>
              <b-button
                variant="primary"
                :disabled="!isPhoto"
                block
                size="sm"
                @click="convertImageToBase64"
                class="mt-2"
              >Скачать</b-button>
              <b-button
                variant="danger"
                :disabled="!isPhoto"
                block
                size="sm"
                @click="deleteCover"
                class="mt-2"
              >Очистить</b-button>
            </div>
            <input id="photo-loader" type="file" @change="handleFileChange" accept=".jpg, .png" />
          </div>
        </b-col>
        <b-col sm="9">
          <b-container fluid class="px-0">
            <!-- ###################################### Rollup 1 ###################################-->
            <div class="colappse-header">
              <span>Персональные данные</span>
              <b-btn
                @click="showRollup1 = !showRollup1"
                :class="showRollup1 ? 'collapsed' : null"
                aria-controls="rollup-1"
                :aria-expanded="showRollup1 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showRollup1" />
                <font-awesome-icon icon="chevron-down" v-else />
              </b-btn>
            </div>
            <b-collapse id="rollup-1" class="collapse-wrap" v-model="showRollup1">
              <b-row>
                <b-col sm="4">
                  <b-form-group
                    id="rollup-1-input-group-1"
                    label-for="rollup-input-1"
                    label-size="sm"
                  >
                    <span slot="label">
                      Имя:
                    </span>
                    <b-form-input
                      id="rollup-1-input-1"
                      type="text"
                      v-model="form.ar_first_name"
                      required
                      placeholder="Введите имя"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col sm="4">
                  <b-form-group
                    id="rollup-1-input-group-2"
                    label-for="rollup-input-2"
                    label-size="sm"
                  >
                    <span slot="label">
                      Отчество:
                    </span>
                    <b-form-input
                      id="rollup-1-input-2"
                      type="text"
                      v-model="form.ar_middle_name"
                      required
                      placeholder="Введите отчество"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col sm="4">
                  <b-form-group
                    id="rollup-1-input-group-3"
                    label-for="rollup-input-3"
                    label-size="sm"
                  >
                    <span slot="label" title="Обязательное поле">
                      Фамилия
                      <span class="label-asterix">*</span>:
                    </span>
                    <b-form-input
                      id="rollup-1-input-3"
                      type="text"
                      v-model="form.ar_last_name"
                      required
                      placeholder="Введите фамилию"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <!-- Разделитель -->
              <b-row>
                <b-col sm="4">
                  <b-form-group
                    id="rollup-1-input-group-1"
                    label-for="rollup-input-1"
                    label-size="sm"
                  >
                    <span slot="label">
                      Псевдоним:
                    </span>
                    <b-form-input
                      id="rollup-1-input-1"
                      type="text"
                      v-model="form.ar_penname"
                      required
                      placeholder="Введите псевдоним"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col sm="4">
                  <b-form-group
                    id="rollup-1-input-group-2"
                    label-for="rollup-input-2"
                    label-size="sm"
                  >
                    <span slot="label">
                      E-mail:
                    </span>
                    <b-form-input
                      id="rollup-1-input-2"
                      type="text"
                      v-model="form.ar_email"
                      required
                      placeholder="Введите E-mail"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col sm="4">
                  <b-form-group
                    id="rollup-1-input-group-3"
                    label-for="rollup-input-3"
                    label-size="sm"
                  >
                    <span slot="label">
                      Сайт:
                    </span>
                    <b-form-input
                      id="rollup-1-input-3"
                      type="text"
                      v-model="form.ar_site"
                      required
                      placeholder="Введите сайт"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-collapse>
             <!-- ###################################### Rollup 2 ###################################-->
             <div class="colappse-header">
              <span>Биография</span>
              <b-btn
                @click="showRollup2 = !showRollup2"
                :class="showRollup2 ? 'collapsed' : null"
                aria-controls="rollup-2"
                :aria-expanded="showRollup2 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showRollup2"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="rollup-2" class="collapse-wrap" v-model="showRollup2">
              <b-row>
                <b-col>
                  <b-form-group
                    id="rollup-2-input-group-1"
                    label="Биография:"
                    label-for="rollup-2-textarea-1"
                    label-size="sm"
                  >
                    <b-form-textarea
                      id="rollup-2-textarea-1"
                      v-model="form.ar_biography"
                      size="sm"
                      :rows="3"
                    ></b-form-textarea>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-collapse>
            <!-- ###################################### Rollup 3 ###################################-->
             <div class="colappse-header">
              <span>Библиография</span>
              <b-btn
                @click="showRollup3 = !showRollup2"
                :class="showRollup3 ? 'collapsed' : null"
                aria-controls="rollup-3"
                :aria-expanded="showRollup3 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showRollup3"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="rollup-3" class="collapse-wrap" v-model="showRollup3">
              <b-row>
                <b-col>
                  <b-form-group
                    id="rollup-3-input-group-1"
                    label="Библиография:"
                    label-for="rollup-3-textarea-1"
                    label-size="sm"
                  >
                    <b-form-textarea
                      id="rollup-3-textarea-1"
                      v-model="form.ar_bibliography"
                      size="sm"
                      :rows="3"
                    ></b-form-textarea>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-collapse>
          </b-container>
        </b-col>
      </b-form-row>
    </b-container>
  </b-modal>
</template>

<style lang="scss">
.ae-modal-content {
  overflow-x: hidden;
  overflow-y: scroll;
}
</style>

<script>
import axios from "axios";
import store from "../../../store";

const shiftL = 225;
const shiftR = 6;

export default {
  name: "modal-ae",
  data() {
    return {
      atID: null,
      mHeight: 100,
      pHeight: 100,
      showRollup1: true,
      showRollup2: true,
      showRollup3: true,
      photoImage: "",
      photoRatio: 1,
      isPhoto: false,
      form: {
        ar_first_name: "",
        ar_middle_name: "",
        ar_last_name: "",
        ar_penname: "",
        ar_email: "",
        ar_site: "",
        ar_biography: "",
        ar_bibliography: ""
      }
    };
  },
  mounted() {
    const self = this;

    window.addEventListener("resize", this.handleResize);
    this.mHeight = window.innerHeight - shiftL;
    if (this.mHeight > 1200) {
      this.mHeight = 1200;
    }

    //список языков

    this.callApi(
      this.$store.getters.prefix + "/static/api.php",
      {
        cmd: "lg_list",
        dat: ""
      },
      "",
      function(rd) {
        self.s2OptionsLang = rd;
        self.s2OptionsOrigLang = rd;
      }
    );
    //Список жанров
    this.callApi(
      this.$store.getters.prefix + "/static/api.php",
      {
        cmd: "bg_list",
        dat: ""
      },
      "",
      function(rd) {
        self.s2OptionsGenres = rd;
      }
    );
    self.form.bk_authors = null;
    self.form.bk_genres = null;
  },
  beforeDestroy: function() {
    window.removeEventListener("resize", this.handleResize);
  },
  methods: {
    closeAuthorEditor() {
      this.form.bk_genres = "";
    },
    shownAuthorEditor() {
      document.getElementById("author" + this.atID).scrollTop = 0;
      ///////////////////////////// драг&дроп обложки (начало) ////////////////////////////////////
      const self = this;
      var dropArea = document.getElementById("photo-drop-area");
      ["dragenter", "dragover", "dragleave", "drop"].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
      });

      function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
      }

      ["dragenter", "dragover"].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
      });

      ["dragleave", "drop"].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
      });

      function highlight(e) {
        dropArea.classList.add("highlight");
      }

      function unhighlight(e) {
        dropArea.classList.remove("highlight");
      }

      dropArea.addEventListener("drop", handleDrop, false);

      function handleDrop(e) {
        self.photoImage = URL.createObjectURL(e.dataTransfer.files[0]);
        self.isPhoto = true;
      }
      ///////////////////////////// драг&дроп обложки (конец) ////////////////////////////////////
    },
    showAuthorEditor() {
      //id выбраного автора
      this.atID = this.$store.getters.authorID;
      const self = this;
      ////получаем расширенную информацию об авторе
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "author_ex",
          dat: self.atID
        },
        "",
        function(rd) {
          if (rd[0].ar_photo) {
            self.isPhoto = true;
            self.photoImage = "data:image/jpg;base64," + rd[0].ar_photo;
          } else {
            self.isPhoto = false;
            self.photoImage = "/static/assets/nophoto.jpg";
          }
          self.form.ar_first_name = rd[0].ar_first_name;
          self.form.ar_middle_name = rd[0].ar_middle_name;
          self.form.ar_last_name = rd[0].ar_last_name;
          self.form.ar_penname = rd[0].ar_penname;
          self.form.ar_email = rd[0].ar_email;
          self.form.ar_site = rd[0].ar_site;
          self.form.ar_biography = rd[0].ar_biography;
          self.form.ar_bibliography = rd[0].ar_bibliography;
        }
      );
      //список всех авторов
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "a_list",
          dat: "simple"
        },
        "",
        function(rd) {
          self.s2OptionsAuthors = rd; //возвр. данные (Responce)
        }
      );
      //список всех серий
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "sa_list",
          dat: "simple"
        },
        "",
        function(rd) {
          self.s2OptionsSeries = rd;
        }
      );
      //Серия
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "b_ser",
          dat: self.atID
        },
        "",
        function(rd) {
          self.form.bk_series = rd; //возвр. данные (Responce)
        }
      );
      //Язык
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "b_lang",
          dat: self.atID
        },
        "",
        function(rd) {
          self.form.bk_language = rd; //возвр. данные (Responce)
        }
      );
      //Язык оригинала
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "b_lang_src",
          dat: self.atID
        },
        "",
        function(rd) {
          self.form.bk_orig_language = rd; //возвр. данные (Responce)
        }
      );

      //жанры книги
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "b_genres",
          dat: self.atID
        },
        "",
        function(rd) {
          self.form.bk_genres = rd; //возвр. данные (Responce)
        }
      );
    },
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
      e.preventDefault();
      this.photoImage = URL.createObjectURL(e.target.files[0]);
      this.isPhoto = true;
    },
    deleteCover() {
      this.isPhoto = false;
      this.photoImage = "/static/assets/nocover.jpg";
    },
    convertImageToBase64() {
      const self = this;
      let img = document.createElement("img");
      let url = this.photoImage;
      img.src = url;
      img.onload = function() {
        let key = encodeURIComponent(url),
          canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;
        let ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);
        self.photoImage = canvas.toDataURL("image/png");
      };
    }
  }
};
</script>
