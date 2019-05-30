<template>
  <div id="tba" class="tba">
    <div class="tb-header">
      <div class="tb-header-left" @click="sortText">
        <div class="tb-header-title">Авторы</div>
        <div class="tb-header-sort-arrows">
          <span class="tb-header-sort-desc" :class="{ active: aDesc }">&#8593;</span>
          <span class="tb-header-sort-asc" :class="{ active: aAsc }">&#8595;</span>
        </div>
      </div>
      <div class="tb-header-right" @click="sortNumeric">
        <div class="tb-header-title">Книг</div>
        <div class="tb-header-sort-arrows">
          <span class="tb-header-sort-desc" :class="{ active: bDesc }">&#8593;</span>
          <span class="tb-header-sort-asc" :class="{ active: bAsc }">&#8595;</span>
        </div>
      </div>
    </div>
    <div class="tb-body" @scroll="onScroll">
      <div
        class="tb-body-element"
        v-for="item in aItems"
        :id="item.id"
        :key="item.id"
        :ref="item.id"
        v-if="aFilter == '*' || aFilter == item.author.charAt(0).toUpperCase()"
        :class="{active: item.isActive}"
        @click="itemClickHandler"
        @mouseover="mouseOverAuthor"
        @mouseleave="mouseLeaveAuthor"
      >
        <div class="tb-body-element-left">{{ item.author }}</div>
        <div class="tb-body-element-right">{{ item.books }}</div>
      </div>
    </div>
    <transition name="fade">
      <div
        v-show="aMenu"
        class="author-menu"
        :style="{ top: aMenuY + 'px', left: aMenuX + 'px' }"
        @mouseover="mouseOverAuthorMenu"
        @mouseleave="mouseLeaveAuthor"
      >
        <b-button-toolbar key-nav aria-label="Toolbar with button groups">
          <b-button-group class="mx-1" size="sm">
            <b-btn variant="primary" title="Редактировать информацию об авторе">
              <font-awesome-icon icon="address-card"/>
            </b-btn>
          </b-button-group>
          <b-button-group class="mx-1" size="sm">
            <b-btn
              variant="warning"
              title="Сделать синонимом..."
              @click="synonymAuthorClick"
              v-b-modal.a-modal2
            >
              <font-awesome-icon icon="user-friends"/>
            </b-btn>
            <b-btn
              variant="warning"
              title="Объединить автора с..."
              @click="combineAuthorClick"
              v-b-modal.a-modal2
            >
              <font-awesome-icon icon="user-minus"/>
            </b-btn>
          </b-button-group>
          <b-button-group class="mx-1" size="sm">
            <b-btn variant="danger" title="Удалить Автора" v-b-modal.a-modal4>
              <font-awesome-icon icon="user-times"/>
            </b-btn>
          </b-button-group>
        </b-button-toolbar>
      </div>
    </transition>
    
    <b-modal
      id="a-modal2"
      size="lg"
      no-close-on-backdrop
      :title="modalHeaderText"
      @ok="modal3HandleOk"
      @show="modal2Show"
      @shown="modal2Shown"
      :ok-title="modalOkText"
      :ok-disabled="disabledOkInModal"
      ok-variant="warning"
      cancel-title="Отмена"
    >
      <b-row class="justify-content-end">
        <b-col md="6" class="mb-3">
          <b-input-group size="sm">
            <b-form-input v-model="filter" placeholder="Фильтр"/>
            <b-input-group-append>
              <b-btn :disabled="!filter" @click="filter = ''">
                <font-awesome-icon icon="times"/>
              </b-btn>
            </b-input-group-append>
          </b-input-group>
        </b-col>
      </b-row>
      <div class="table-wrap">
        <div class="modal-tb-header" @click="sortAuthor">
          <div class="modal-tb-header-left">Авторы</div>
          <div class="modal-tb-header-right">
            <span class="modal-tb-header-right-asc" :class="{ active: mSort }">&#8593;</span>
            <span class="modal-tb-header-right-desc" :class="{ active: !mSort }">&#8595;</span>
          </div>
        </div>
        <div
          class="table-body-wrap"
          :style="{ maxHeight: mHeight + 'px', minHeight: mHeight + 'px' }"
        >
          <b-table
            hover
            :items="modal2Items"
            :fields="fields"
            thead-class="b-table-head"
            tbody-class="b-table-body"
            :filter="filter"
            :sort-by.sync="sortBy"
            :sort-desc.sync="mSort"
            @filtered="onFiltered"
            @row-clicked="onRowClicked"
          ></b-table>
        </div>
      </div>
    </b-modal>
    <b-modal
      id="a-modal3"
      size="lg"
      no-close-on-backdrop
      title="Объединение авторов"
      ref="scModalRef"
      @ok="scHandleOk"
      ok-title="Да"
      cancel-title="Нет"
    >
      <b-row>
        <b-col md="1">
          <font-awesome-icon class="question-modal-icon" icon="question-circle"/>
        </b-col>
        <b-col>
          <h5 v-if="authorOperation == 'synonym'">
            Вы действительно хотите сделать
            <b>{{ this.authorName }}</b> синонимом автора
            <b>{{ this.selectedAuthorInModal }}</b>?
          </h5>
          <h5 v-if="authorOperation == 'combine'">
            Вы действительно хотите объединить
            <b>{{ this.authorName }}</b> c
            <b>{{ this.selectedAuthorInModal }}</b>? При этом все книги
            <b>{{ this.authorName }}</b> будут перенесены автору
            <b>{{ this.selectedAuthorInModal }}</b>. Исходный автор будет удален.
          </h5>
        </b-col>
      </b-row>
    </b-modal>
    <b-modal
      id="a-modal4"
      size="lg"
      no-close-on-backdrop
      title="Удаление автора"
      @ok="delHandleOk"
      @shown="hideMenu"
      ok-title="Удалить"
      ok-variant="danger"
      cancel-title="Отмена"
    >
      <b-row>
        <b-col md="1">
          <font-awesome-icon class="question-modal-icon" icon="question-circle"/>
        </b-col>
        <b-col>
          <h4>Вы действительно хотите удалить выбранного автора и все его книги из библиотеки?</h4>
          <b-form-checkbox
            id="checkbox1"
            class="mt-2"
            v-model="status"
            value="yes"
            unchecked-value="no"
          >Так же удалить все написанные в соавторстве книги</b-form-checkbox>
        </b-col>
      </b-row>
    </b-modal>
  </div>
</template>

<style lang="scss">
.author-menu {
  position: absolute;
  display: flex;
  width: auto;
  height: auto !important;
  z-index: 10;
  top: 0;

  .btn:focus {
    box-shadow: none !important;
  }
}
</style>

<script>
import store from "../../../../store";

function sortTasc(a, b) {
  let x = a.author.toLowerCase();
  let y = b.author.toLowerCase();
  return x < y ? -1 : x > y ? 1 : 0;
}

function sortTdesc(a, b) {
  let x = a.author.toLowerCase();
  let y = b.author.toLowerCase();
  return x > y ? -1 : x < y ? 1 : 0;
}

function sortNasc(a, b) {
  let x = a.books;
  let y = b.books;
  return x < y ? -1 : x > y ? 1 : 0;
}

function sortNdesc(a, b) {
  let x = a.books;
  let y = b.books;
  return x > y ? -1 : x < y ? 1 : 0;
}

const shiftL = 515;

export default {
  name: "items-list",
  props: ["aItems", "aFilter"],
  data: function() {
    return {
      authorID: "",
      //кого заменяем
      authorName: "",
      status: "no",
      aAsc: true,
      aDesc: false,
      bAsc: false,
      bDesc: false,
      //
      aMenu: false,
      aMenuX: 0,
      aMenuY: 0,
      //modal 23
      mHeight: 100,
      fields: {
        author: {
          label: "",
          sortable: true
        }
      },
      modal2Items: [],
      sortBy: "author",
      mSort: false,
      filter: null,
      //на кого заменяем
      selectedAuthorInModal: "",
      disabledOkInModal: true,
      //для запоминания Eventa в onRowClicked
      modalEvent: [],
      modalHeaderText: "",
      modalOkText: "",
      authorOperation: ""
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
  computed: {
    sortOptions() {
      // Create an options list from our fields
      return this.fields
        .filter(f => f.sortable)
        .map(f => {
          return { text: f.label, value: f.key };
        });
    },
  },
  methods: {
    //modal 2-3
    synonymAuthorClick() {
      this.modalHeaderText = "Сделать «" + this.authorName + "» синонимом...";
      this.modalOkText = "OK";
      this.authorOperation = "synonym";
    },
    combineAuthorClick() {
      this.modalHeaderText = "Объединить «" + this.authorName + "» с...";
      this.modalOkText = "Объединить";
      this.authorOperation = "combine";
    },
    handleResize() {
      this.mHeight = window.innerHeight - shiftL;
      if (this.mHeight > 1200) {
        this.mHeight = 1200;
      }
    },
    removeSelectionInModalTable() {
      let el = document.getElementsByClassName("b-table-body")[0];
      if (el) {
        el.parentElement
          .querySelectorAll(".active")
          .forEach(e => e.classList.remove("active"));
        this.disabledOkInModal = true;
      }
    },
    sortAuthor() {
      this.mSort = !this.mSort;
      this.$nextTick(function() {
        if (this.selectedAuthorInModal) {
          this.removeSelectionInModalTable();
          let el = document.getElementsByClassName("b-table-body")[0];
          for (let i = 0; i < el.children.length; i++) {
            if (
              el.children[i].innerText === this.selectedAuthorInModal.trim()
            ) {
              el.children[i].className += "active";
              this.disabledOkInModal = false;
            }
          }
        }
      });
    },
    onFiltered(filteredItems) {
      this.removeSelectionInModalTable();
    },
    onRowClicked(item, index, event) {
      this.removeSelectionInModalTable();
      this.selectedAuthorInModal = item.author;
      this.modalEvent = event;
      event.target.className += "active";
      this.disabledOkInModal = false;
    },
    modal2Show() {
      this.modal2Items = [];
      for (let i = 0; i < this.aItems.length; i++) {
        if (this.aItems[i].author != this.authorName) {
          this.modal2Items.push({ author: this.aItems[i].author });
        }
      }
      this.aMenu = false;
    },
    modal2Shown() {
      this.selectedAuthorInModal = "";
      this.selectedInModal = false;
      this.removeSelectionInModalTable();
    },
    modal3HandleOk() {
      this.$refs.scModalRef.show();
    },
    scHandleOk() {
      // const self = this;
      // this.callApi(
      //   this.$store.getters.prefix + "/static/api.php",
      //   {
      //     cmd: "del_author",
      //     id: this.authorID,
      //     check: this.status
      //   },
      //   "",
      //   function(rd) {}
      // );
    },
    // modal 2-3 end
    itemClickHandler(item) {
      //сбросим атрибуты по всему массиву
      this.aItems.forEach(function(entry) {
        entry.isActive = false;
      });
      let element = this.aItems[
        this.aItems.map(el => el.id).indexOf(item.currentTarget.id)
      ];
      this.authorID = element.id.substr(2);
      this.authorName = element.author;
      store.commit("setAuthorID", this.authorID);
      element.isActive = true;
      this.aMenu = true;
      this.menuPos(item.currentTarget.id);
    },
    menuPos(id) {
      if (this.aMenu) {
        //размеры меню
        let mW = 149,
          mH = 32;
        //координаты родителя
        let p = document.getElementById("tba").getBoundingClientRect();
        //координаты относительно родителя
        let c = document.getElementById(id).getBoundingClientRect();
        this.aMenuX = c.left + (c.width - mW) / 2 - p.left;
        if (this.getViewportHeight() > c.bottom + p.top - mH * 2.5) {
          this.aMenuY = c.bottom - p.top - 3;
        } else {
          this.aMenuY = c.top - p.top - mH + 3;
        }
      }
    },
    mouseOverAuthor(item) {
      let element = this.aItems[
        this.aItems.map(el => el.id).indexOf(item.currentTarget.id)
      ];
      this.aMenu = element.isActive || false;
      this.menuPos(item.currentTarget.id);
    },
    mouseLeaveAuthor() {
      this.aMenu = false;
    },
    mouseOverAuthorMenu() {
      this.aMenu = true;
    },
    onScroll() {
      this.aMenu = false;
    },
    hideMenu() {
      this.aMenu = false;
    },
    delHandleOk() {
      const self = this;
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "del_author",
          id: this.authorID,
          check: this.status,
          uid: sessionStorage.getItem('user-login')
        },
        "",
        function(rd) {}
      );
    },
    sortText() {
      this.bAsc = false;
      this.bDesc = false;
      if (this.aAsc) {
        this.aDesc = true;
        this.aAsc = false;
      } else if (this.aDesc) {
        this.aDesc = false;
        this.aAsc = true;
      } else {
        this.aDesc = false;
        this.aAsc = true;
      }
      if (this.aAsc) this.aItems.sort(sortTasc);
      else this.aItems.sort(sortTdesc);
    },
    sortNumeric() {
      this.aAsc = false;
      this.aDesc = false;
      if (this.bAsc) {
        this.bDesc = true;
        this.bAsc = false;
      } else if (this.bDesc) {
        this.bDesc = false;
        this.bAsc = true;
      } else {
        this.bDesc = false;
        this.bAsc = true;
      }
      if (this.bAsc) this.aItems.sort(sortNasc);
      else this.aItems.sort(sortNdesc);
    }
  },
  //Хук <UPDATED> вызывается после изменения данных в компоненте и перерисовки DOM
  updated: function() {
    if (store.getters.authorID === -1) {
      this.aItems.forEach(function(entry) {
        entry.isActive = false;
      });
    }
  }
};
</script>
