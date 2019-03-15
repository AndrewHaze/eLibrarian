<template>
  <b-modal
    id="bookEditor"
    ref="modal-e"
    @show="showBookEditor"
    @shown="shownBookEditor"
    @hidden="closeBookEditor"
    no-close-on-backdrop
    size="max"
    title="Книга"
    ok-title="Сохранить"
    cancel-title="Отмена"
  >
    <b-container
      :id="'book'+bkID"
      fluid
      class="be-modal-content"
      :style="{ maxHeight: mHeight + 'px', minHeight: mHeight + 'px' }"
    >
      <b-form-row>
        <b-col sm="3" class="cover-wrap">
          <div class="cover-wrap-sticky">
            <div id="drop-area">
              <b-img thumbnail fluid :src="coverImage"/>
            </div>
            <div>
              <b-button variant="success" block size="sm" @click="openFiles" class="mt-2">Загрузить</b-button>
              <b-button
                variant="primary"
                :disabled="!isCover"
                block
                size="sm"
                @click="convertImageToBase64"
                class="mt-2"
              >Скачать</b-button>
              <b-button
                variant="danger"
                :disabled="!isCover"
                block
                size="sm"
                @click="deleteCover"
                class="mt-2"
              >Очистить</b-button>
            </div>
            <input id="cover-loader" type="file" @change="handleFileChange" accept=".jpg, .png">
          </div>
        </b-col>
        <b-col sm="9">
          <b-container fluid class="px-0">
            <!-- ###################################### colappse 1 ###################################-->
            <div class="colappse-header">
              <span>Книга</span>
              <b-btn
                @click="showCollapse1 = !showCollapse1"
                :class="showCollapse1 ? 'collapsed' : null"
                aria-controls="collapse-1"
                :aria-expanded="showCollapse1 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse1"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse-1" class="collapse-wrap" v-model="showCollapse1">
              
              <b-form-group
                id="collapse-1-input-group-1"
                label-for="collapse1-input-1"
                label-size="sm"
              >
                <span slot="label" title="Обязательное поле">Название<span class="label-asterix">*</span>:</span>
                <b-form-input
                  id="collapse-1-input-1"
                  type="text"
                  v-model="form.bk_title"
                  required
                  placeholder="Введите название книги"
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse-1-input-group-2"
                label="Оригинальное название:"
                label-for="collapse-1-input-2"
                label-size="sm"
              >
                <b-form-input
                  id="collapse-1-input-2"
                  type="text"
                  v-model="form.bk_original_title"
                  placeholder="Введите оригинальное название книги"
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse-1-input-group-3"
                label-for="collapse-1-select-1"
                label-size="sm"
              >
                <span slot="label" title="Обязательное поле">Авторы<span class="label-asterix">*</span>:</span>
                <multiselect
                  id="collapse-1-select-1"
                  :multiple="true"
                  placeholder="Выберите автора"
                  selectLabel="Нажмите Enter, чтобы выбрать"
                  selectedLabel="Выбран"
                  deselectLabel="Нажмите Enter, чтобы отменить выбор"
                  label="author"
                  track-by="id"
                  v-model="form.bk_authors"
                  :options="s2OptionsAuthors"
                >
                  <span slot="noResult">Совпадений нет. Попробуйте изменить поисковый запрос</span>
                </multiselect>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse-1-input-group-4"
                label="Жанры:"
                label-for="collapse-1-select-2"
                label-size="sm"
              >
                <multiselect
                  id="collapse-1-select-2"
                  :multiple="true"
                  v-model="form.bk_genres"
                  placeholder="Выберите жанр"
                  selectLabel="Нажмите Enter, чтобы выбрать"
                  selectedLabel="Выбран"
                  deselectLabel="Нажмите Enter, чтобы отменить выбор"
                  group-values="genres"
                  group-label="group"
                  label="title"
                  track-by="code"
                  :options="s2OptionsGenres"
                >
                  <span slot="noResult">Совпадений нет. Попробуйте изменить поисковый запрос</span>
                </multiselect>
              </b-form-group>

              <b-row>
                <b-col>
                  <b-form-group
                    id="collapse-1-input-group-5"
                    label="Серия:"
                    label-for="collapse-1-select-3"
                    label-size="sm"
                  >
                    <multiselect
                      id="collapse-1-select-3"
                      v-model="form.bk_series"
                      :options="s2OptionsSeries"
                      placeholder="Выберите серию"
                      selectLabel="Нажмите Enter, чтобы выбрать"
                      selectedLabel="Выбрана"
                      deselectLabel="Нажмите Enter, чтобы отменить выбор"
                      label="seriesTitle"
                      track-by="id"
                    >
                      <span slot="noResult">Совпадений нет. Попробуйте изменить поисковый запрос</span>
                    </multiselect>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="2">
                  <b-form-group
                    id="collapse-2-input-group-2"
                    label="Номер:"
                    label-for="collapse-2-input-2"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse-2-input-2"
                      type="number"
                      min="0"
                      v-model="form.bk_seriesNumber"
                      placeholder="№ серии"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-collapse>
            <!-- ###################################### colappse 3 ###################################-->
            <div class="colappse-header">
              <span>Дополнительно</span>
              <b-btn
                @click="showCollapse3 = !showCollapse3"
                :class="showCollapse3 ? 'collapsed' : null"
                aria-controls="collapse-3"
                :aria-expanded="showCollapse3 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse3"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse-3" class="collapse-wrap" v-model="showCollapse3">
              <b-row>
                <b-col sm="4">
                  <b-form-group
                    id="collapse-3-input-group-1"
                    label="Дата:"
                    label-for="collapse-3-input-1"
                    label-size="sm"
                  >
                    <b-form-input id="collapse-3-input-1" type="date" v-model="form.bk_date" size="sm"></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="4">
                  <b-form-group
                    id="collapse-3-input-group-2"
                    label="Язык:"
                    label-for="collapse-3-select-1"
                    label-size="sm"
                  >
                    <multiselect
                      id="collapse-3-select-1"
                      v-model="form.bk_language"
                      :options="s2OptionsLang"
                      placeholder="Выберите язык"
                      selectLabel="Нажмите Enter, чтобы выбрать"
                      selectedLabel="Выбран"
                      deselectLabel="Нажмите Enter, чтобы отменить выбор"
                      label="lg_name"
                      track-by="lg_name"
                    >
                      <!-- track-by с id глючит. ???? -->
                      <span slot="noResult">Совпадений нет. Попробуйте изменить поисковый запрос</span>
                    </multiselect>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="4">
                  <b-form-group
                    id="collapse-3-input-group-3"
                    label="Язык оригинала:"
                    label-for="collapse-3-input-3"
                    label-size="sm"
                  >
                    <multiselect
                      id="collapse-3-select-2"
                      v-model="form.bk_orig_language"
                      :options="s2OptionsOrigLang"
                      placeholder="Выберите язык оригинала"
                      selectLabel="Нажмите Enter, чтобы выбрать"
                      selectedLabel="Выбран"
                      deselectLabel="Нажмите Enter, чтобы отменить выбор"
                      label="lg_name"
                      track-by="lg_name"
                    >
                      <!-- track-by с id глючит. ???? -->
                      <span slot="noResult">Совпадений нет. Попробуйте изменить поисковый запрос</span>
                    </multiselect>
                  </b-form-group>
                </b-col>
              </b-row>
              <!-- Разделитель -->
              <b-form-group
                id="collapse-3-input-group-4"
                label="Ключевые слова:"
                label-for="collapse-3-input-4"
                label-size="sm"
              >
                <b-form-input
                  id="collapse-3-input-4"
                  type="text"
                  v-model="form.bk_keywords"
                  placeholder="Введите ключевые слова"
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse-3-input-group-5"
                label="Переводчики:"
                label-for="collapse-3-input-5"
                label-size="sm"
              >
                <b-form-input
                  id="collapse-3-input-5"
                  type="text"
                  v-model="form.bk_translators"
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
                aria-controls="collapse-4"
                :aria-expanded="showCollapse4 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse4"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse-4" class="collapse-wrap" v-model="showCollapse4">
              <b-row>
                <b-col>
                  <b-form-group
                    id="collapse-4-input-group-1"
                    label="Аннотация:"
                    label-for="collapse-4-input-1"
                    label-size="sm"
                  >
                    <b-form-textarea
                      id="collapse-4-textarea-1"
                      v-model="form.bk_annotation"
                      size="sm"
                      :rows="3"
                    ></b-form-textarea>
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
                aria-controls="collapse-5"
                :aria-expanded="showCollapse5 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse5"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse-5" class="collapse-wrap" v-model="showCollapse5">
              <b-form-group
                id="collapse-5-input-group-1"
                label="Название публикации:"
                label-for="collapse-5-input-1"
                label-size="sm"
              >
                <b-form-input
                  id="collapse-5-input-1"
                  type="text"
                  v-model="form.bk_pub_title"
                  placeholder="Введите название публикации"
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse-5-input-group-2"
                label="Издатель:"
                label-for="collapse-5-input-2"
                label-size="sm"
              >
                <b-form-input
                  id="collapse-5-input-2"
                  type="text"
                  v-model="form.bk_pub_publisher"
                  placeholder="Введите издателя"
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-row>
                <b-col sm="5">
                  <b-form-group
                    id="collapse-5-input-group-4"
                    label="Город:"
                    label-for="collapse-5-input-4"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse-5-input-4"
                      type="text"
                      v-model="form.bk_pub_city"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="2">
                  <b-form-group
                    id="collapse-5-input-group-5"
                    label="Год издания:"
                    label-for="collapse-5-input-5"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse-5-input-5"
                      type="text"
                      v-model="form.bk_pub_year"
                      placeholder="Введите год издания"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="5">
                  <b-form-group
                    id="collapse-5-input-group-6"
                    label="ISBN:"
                    label-for="collapse-5-input-6"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse-5-input-6"
                      type="text"
                      v-model="form.bk_pub_isbn"
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
                aria-controls="collapse-6"
                :aria-expanded="showCollapse6 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse6"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse-6" class="collapse-wrap" v-model="showCollapse6">
              <b-row>
                <b-col sm="2">
                  <b-form-group
                    id="collapse-6-input-group-3"
                    label="Версия:"
                    label-for="collapse-6-input-3"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse-6-input-3"
                      type="text"
                      v-model="form.bk_doc_ver"
                      placeholder="Введите версию документа"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="6">
                  <b-form-group
                    id="collapse-6-input-group-1"
                    label="ID документа:"
                    label-for="collapse-6-input-1"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse-6-input-1"
                      type="text"
                      v-model="form.bk_doc_id"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="4">
                  <b-form-group
                    id="collapse-6-input-group-2"
                    label="Дата:"
                    label-for="collapse-6-input-2"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse-6-input-2"
                      type="date"
                      v-model="form.bk_doc_date"
                      placeholder="Введите дату документа"
                      size="sm"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <!-- Разделитель -->
              <b-row>
                <b-col>
                  <b-form-group
                    id="collapse-6-input-group-8"
                    label="История:"
                    label-for="collapse-6-input-8"
                    label-size="sm"
                  >
                    <b-form-textarea
                      id="collapse-6-Textarea-8"
                      v-model="form.bk_doc_history"
                      size="sm"
                      :rows="3"
                    ></b-form-textarea>
                  </b-form-group>
                </b-col>
              </b-row>
              <!-- Разделитель -->
              <b-form-group
                id="collapse-6-input-group-4"
                label="Авторы:"
                label-for="collapse-6-input-4"
                label-size="sm"
              >
                <b-form-input
                  id="collapse-6-input-4"
                  type="text"
                  v-model="form.bk_doc_authors"
                  placeholder
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse-6-input-group-5"
                label="Программы:"
                label-for="collapse-6-input-5"
                label-size="sm"
              >
                <b-form-input
                  id="collapse-6-input-5"
                  type="text"
                  v-model="form.bk_doc_programms"
                  placeholder
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse-6-input-group-6"
                label="Исходный URL:"
                label-for="collapse-6-input-6"
                label-size="sm"
              >
                <b-form-input
                  id="collapse-6-input-6"
                  type="text"
                  v-model="form.bk_doc_url"
                  placeholder
                  size="sm"
                ></b-form-input>
              </b-form-group>
              <!-- Разделитель -->
              <b-form-group
                id="collapse-6-input-group-7"
                label="Автор OCR:"
                label-for="collapse-6-input-7"
                label-size="sm"
              >
                <b-form-input
                  id="collapse-6-input-7"
                  type="text"
                  v-model="form.bk_doc_ocr_authors"
                  placeholder
                  size="sm"
                ></b-form-input>
              </b-form-group>
            </b-collapse>
            <!-- ###################################### colappse 7 ###################################-->
            <div class="colappse-header">
              <span>Служебная информация</span>
              <b-btn
                @click="showCollapse7 = !showCollapse7"
                :class="showCollapse7 ? 'collapsed' : null"
                aria-controls="collapse-7"
                :aria-expanded="showCollapse7 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse7"/>
                <font-awesome-icon icon="chevron-down" v-else/>
              </b-btn>
            </div>
            <b-collapse id="collapse-7" class="collapse-wrap" v-model="showCollapse7">
              <b-row>
                <b-col sm="4">
                  <b-form-group
                    id="collapse-7-input-group-1"
                    label="Дата файла:"
                    label-for="collapse-7-input-1"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse-7-input-1"
                      type="date"
                      v-model="form.bk_doc_file_date"
                      size="sm"
                      readonly
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="4">
                  <b-form-group
                    id="collapse-7-input-group-2"
                    label="Размер файла:"
                    label-for="collapse-7-input-2"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse-7-input-2"
                      type="text"
                      v-model="form.bk_doc_file_size"
                      size="sm"
                      readonly
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <!-- Разделитель -->
                <b-col sm="4">
                  <b-form-group
                    id="collapse-7-input-group-3"
                    label="Формат:"
                    label-for="collapse-7-input-3"
                    label-size="sm"
                  >
                    <b-form-input
                      id="collapse-7-input-3"
                      type="text"
                      v-model="form.bk_doc_format"
                      size="sm"
                      readonly
                    ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <!-- Разделитель -->
              <b-form-group
                id="collapse-7-input-group-4"
                label="Имя файла:"
                label-for="collapse-7-input-4"
                label-size="sm"
              >
                <b-form-input
                  id="collapse-7-input-4"
                  type="text"
                  v-model="form.bk_doc_file_name"
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

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
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

.highlight {
  border: 2px dashed purple;
  border-radius: 0.2rem;
  padding: 1px;
}

.cover-wrap {
  text-align: center;
  padding-right: 1.5rem !important;
  position: relative;
  min-height: 100%;
  .cover-wrap-sticky {
    position: sticky;
    top: 0;
  }
  .img-thumbnail {
    width: 100%;
  }
}

.colappse-header {
  font-weight: 600;
  overflow: hidden;
  &::after,
  &::before {
    display: inline-block;
    content: "";
    vertical-align: middle;
    box-sizing: border-box;
    width: 100%;
    height: 1px;
    background: #737b83;
    border: solid #fff;
    border-width: 0 2px;
  }
  &::after {
    margin-right: -100%;
  }
  &::before {
    margin-left: -100%;
  }
  span {
    padding-right: 0.3rem;
  }
  .btn-circle {
    position: absolute;
    right: 0;
    width: 20px;
    height: 20px;
    text-align: center;
    padding: 4px 0;
    font-size: 12px;
    line-height: 0;
    border-radius: 10px;
    background-color: #fff;
    margin-top: 5px;
    // &:focus {
    //   box-shadow: none;
    // }
    &:hover {
      background-color: #737b83;
    }
  }
}
.collapse-wrap {
  padding: 0.5rem 1rem 0;
  .col-fix-2btn {
    display: flex;
    flex-flow: column;
    justify-content: flex-end;
    max-width: 5.65rem;
    padding-bottom: 0.1rem;
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

  .form-group {
    > label {
      white-space: nowrap;
      .label-asterix {
        color: red;
      }
    }
  }
}

//Bootstrap form-control-sm скин для multiselect
.multiselect {
  * {
    font-size: 0.875rem;
  }
  min-height: 0;
  .multiselect__tags {
    display: flex;
    align-items: center;
    min-height: 31px;
    padding: 0 30px 0 8px;
    border-color: #ced4da;
    border-radius: 0.2rem;
    overflow: hidden;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    .multiselect__placeholder {
      margin: 0;
      line-height: 1;
      padding-top: 3px;
      min-height: 20px;
      white-space: nowrap;
      overflow: hidden;
    }
    .multiselect__tags-wrap {
      display: flex;
      flex-flow: row wrap;
      align-items: center;
      height: 100%;
      padding-top: 4px;
      .multiselect__tag {
        padding: 3px 26px 4px 10px;
        margin: 0 4px 4px 0;
        .multiselect__tag-icon {
          line-height: 19px;
        }
      }
    }
    .multiselect__input,
    .multiselect__single {
      margin: 0;
      line-height: 18px;
      padding-left: 0;
    }
    input {
      margin: 0;
    }
  }
  .multiselect__select {
    height: 100%;
    width: 30px;
  }
  .multiselect__content-wrapper {
    border-color: #ced4da;
    .multiselect__option {
      min-height: 0;
      padding: 6px 12px;
    }
    .multiselect__option--selected {
      font-weight: 600;
    }
    span:after {
      line-height: 11px;
      padding: 8px;
    }
    .multiselect__option--group {
      text-align: center;
      font-weight: 600;
    }
  }
}
.multiselect--active .multiselect__tags {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>

<script>
import axios from "axios";
import store from "../../../store";
import Multiselect from "vue-multiselect";

const shiftL = 225;
const shiftR = 6;

export default {
  name: "modal-be",
  components: {
    Multiselect
  },
  data() {
    return {
      bkID: null,
      mHeight: 100,
      pHeight: 100,
      showCollapse1: true,
      showCollapse3: true,
      showCollapse4: true,
      showCollapse5: true,
      showCollapse6: true,
      showCollapse7: true,
      coverImage: "",
      coverRatio: 1,
      isCover: false,
      form: {
        bk_authors: null,
        bk_genres: null,
        bk_series: null,
        ///////////////
        bk_seriesNumber: "",
        bk_title: "",
        bk_original_title: "",
        bk_annotation: "",
        bk_date: "",
        bk_language: null,
        bk_orig_language: "",
        bk_file_name: "",
        bk_cover: null,
        bk_keywords: "",
        bk_translators: "",
        ///////////////
        bk_pub_title: "",
        bk_pub_publisher: "",
        bk_pub_city: "",
        bk_pub_year: "",
        bk_pub_isbn: "",
        ///////////////
        bk_doc_id: "",
        bk_doc_date: "",
        bk_doc_ver: "",
        bk_doc_history: "",
        bk_doc_authors: "",
        bk_doc_programms: "",
        bk_doc_url: "",
        bk_doc_ocr_authors: "",
        ///////////////
        bk_doc_file_name: "",
        bk_doc_file_date: "",
        bk_doc_file_size: "",
        bk_doc_format: "fb2"
      },
      s2OptionsAuthors: [],
      s2OptionsGenres: [],
      s2OptionsSeries: [],
      s2OptionsLang: [],
      s2OptionsOrigLang: []
    };
  },
  watch: {},
  mounted: function() {
    const self = this;

    window.addEventListener("resize", this.handleResize);
    this.mHeight = window.innerHeight - shiftL;
    if (this.mHeight > 1200) {
      this.mHeight = 1200;
    }

    ///////////////////////////// драг&дроп обложки (начало) ////////////////////////////////////
    let dropArea = document.getElementById("drop-area");
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
      self.coverImage = URL.createObjectURL(e.dataTransfer.files[0]);
      self.isCover = true;
    }
    ///////////////////////////// драг&дроп обложки (конец) ////////////////////////////////////

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
    closeBookEditor() {
      this.form.bk_genres = "";
    },
    shownBookEditor() {
      document.getElementById("book" + this.bkID).scrollTop = 0;
    },
    showBookEditor() {
      //id текущей книги
      this.bkID = this.$store.getters.bkID;
      const self = this;
      // авторы книги
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "b_authors",
          dat: self.bkID
        },
        "",
        function(rd) {
          self.form.bk_authors = rd; //возвр. данные (Responce)
        }
      );
      //информация по книге
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "book",
          dat: self.bkID
        },
        "",
        function(rd) {
          if (rd[0].bk_cover) {
            self.isCover = true;
            self.coverImage = "data:image/jpg;base64," + rd[0].bk_cover;
          } else {
            self.isCover = false;
            self.coverImage = "/static/assets/nocover.jpg";
          }
          self.form.bk_title = rd[0].bk_title;
          self.form.bk_original_title = rd[0].bk_src_title;
          self.form.bk_seriesNumber = rd[0].bk_seriesNumber;
          self.form.bk_date =
            rd[0].bk_date === "2099-01-01" ? null : rd[0].bk_date;
          self.form.bk_annotation = rd[0].bk_annotation;
          self.form.bk_doc_id = rd[0].bk_doc_id;
          self.form.bk_keywords = rd[0].bk_keywords;
          self.form.bk_translators = rd[0].bk_translators;
          self.form.bk_doc_authors = rd[0].bk_doc_authors;
          self.form.bk_doc_programms = rd[0].bk_doc_programms;
          self.form.bk_doc_id = rd[0].bk_doc_id;
          self.form.bk_doc_date =
            rd[0].bk_doc_date === "2099-01-01" ? null : rd[0].bk_doc_date;
          self.form.bk_doc_ocr_authors = rd[0].bk_doc_ocr_authors;
          self.form.bk_doc_ver = rd[0].bk_doc_ver;
          self.form.bk_doc_url = rd[0].bk_doc_url;
          self.form.bk_doc_history = rd[0].bk_doc_history;
          self.form.bk_pub_title = rd[0].bk_pub_title;
          self.form.bk_pub_publisher = rd[0].bk_pub_publisher;
          self.form.bk_pub_city = rd[0].bk_pub_city;
          self.form.bk_pub_year = rd[0].bk_pub_year;
          self.form.bk_pub_isbn = rd[0].bk_pub_isbn;
          self.form.bk_doc_file_name = rd[0].bk_doc_file_name;
          self.form.bk_doc_file_size = rd[0].bk_doc_file_size;
          self.form.bk_doc_file_date = rd[0].bk_doc_file_date;
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
          dat: self.bkID
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
          dat: self.bkID
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
          dat: self.bkID
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
          dat: self.bkID
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
      this.coverImage = URL.createObjectURL(e.target.files[0]);
      this.isCover = true;
    },
    deleteCover() {
      this.isCover = false;
      this.coverImage = "/static/assets/nocover.jpg";
    },
    convertImageToBase64() {
      const self = this;
      let img = document.createElement("img");
      let url = this.coverImage;
      img.src = url;
      img.onload = function() {
        let key = encodeURIComponent(url),
          canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;
        let ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);
        self.coverImage = canvas.toDataURL("image/png");
      };
    }
  }
};
</script>
