<template>
  <div id="tbs" class="tbs">
    <div class="tb-header">
      <div class="tb-header-left" @click="sortText">
        <div class="tb-header-title">Серии</div>
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
        v-for="item in sItems"
        :id="item.id"
        :key="item.id"
        :ref="item.id"
        v-if="sFilter == '*' || sFilter == item.seriesTitle.charAt(0).toUpperCase()"
        :class="{active: item.isActive}"
        @click="itemClickHandler"
        @mouseover="mouseOverSeries"
        @mouseleave="mouseLeaveSeries"
      >
        <div class="tb-body-element-left">{{ item.seriesTitle }}</div>
        <div class="tb-body-element-right">{{ item.books }}</div>
      </div>
    </div>
    <!-- <transition name="fade">
      <div
        v-show="sMenu"
        class="series-menu"
        :style="{ top: sMenuY + 'px', left: sMenuX + 'px' }"
        @mouseover="mouseOverSeriesMenu"
        @mouseleave="mouseLeaveSeries"
      >
        <b-button-toolbar key-nav aria-label="Toolbar with button groups">
          
          <b-button-group class="mx-1" size="sm">
            <b-btn variant="danger" title="Удалить Автора" v-b-modal.s-modal1>
              <font-awesome-icon icon="user-times"/>
            </b-btn>
          </b-button-group>
        </b-button-toolbar>
      </div>
    </transition> -->
    
    
    <b-modal
      id="s-modal1"
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
.series-menu {
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
  let x = a.seriesTitle.toLowerCase();
  let y = b.seriesTitle.toLowerCase();
  return x < y ? -1 : x > y ? 1 : 0;
}

function sortTdesc(a, b) {
  let x = a.seriesTitle.toLowerCase();
  let y = b.seriesTitle.toLowerCase();
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
  props: ["sItems", "sFilter"],
  data: function() {
    return {
      seriesID: "",
      
      status: "no",
      aAsc: true,
      aDesc: false,
      bAsc: false,
      bDesc: false,
      //
      sMenu: false,
      sMenuX: 0,
      sMenuY: 0,
      
    };
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
    itemClickHandler(item) {
      //сбросим атрибуты по всему массиву
      this.sItems.forEach(function(entry) {
        entry.isActive = false;
      });
      let element = this.sItems[
        this.sItems.map(el => el.id).indexOf(item.currentTarget.id)
      ];
      this.seriesID = element.id.substr(2);
      store.commit("setSeriesID", this.seriesID);
      element.isActive = true;
      this.sMenu = true;
      this.menuPos(item.currentTarget.id);
    },
    menuPos(id) {
      if (this.sMenu) {
        //размеры меню
        let sW = 36,
            sH = 32;
        //координаты родителя
        let p = document.getElementById("tbs").getBoundingClientRect();
        //координаты относительно родителя
        let c = document.getElementById(id).getBoundingClientRect();
        this.sMenuX = c.left + (c.width - sW) / 2 - p.left;
        if (this.getViewportHeight() > c.bottom + p.top - sH * 2.5) {
          this.sMenuY = c.bottom - p.top - 3;
        } else {
          this.sMenuY = c.top - p.top - sH + 3;
        }
      }
    },
    mouseOverSeries(item) {
      let element = this.sItems[
        this.sItems.map(el => el.id).indexOf(item.currentTarget.id)
      ];
      this.sMenu = element.isActive || false;
      this.menuPos(item.currentTarget.id);
    },
    mouseLeaveSeries() {
      this.sMenu = false;
    },
    mouseOverSeriesMenu() {
      this.sMenu = true;
    },
    onScroll() {
      this.sMenu = false;
    },
    hideMenu() {
      this.sMenu = false;
    },
    delHandleOk() {
      const self = this;
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "del_author",
          id: this.seriesID,
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
      if (this.aAsc) this.sItems.sort(sortTasc);
      else this.sItems.sort(sortTdesc);
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
      if (this.bAsc) this.sItems.sort(sortNasc);
      else this.sItems.sort(sortNdesc);
    }
  },
  //Хук <UPDATED> вызывается после изменения данных в компоненте и перерисовки DOM
  updated: function() {
    if (store.getters.seriesID === -1) {
      this.sItems.forEach(function(entry) {
        entry.isActive = false;
      });
    }
  }
};
</script>
