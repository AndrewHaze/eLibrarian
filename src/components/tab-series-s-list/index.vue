<template>
  <div id="tbs" class="tb">
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
    <transition name="fade">
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
    </transition>
    
    
    <b-modal
      id="s-modal1"
      size="lg"
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
$line-color: #dee2e6;
$header-font-color: #495057;
$selected-color: #ddd;
$hover-color: rgba(221, 221, 221, 0.4);

%flex {
  display: flex;
  flex: row, nowrap;
  justify-content: space-between;
}

.table-wrap {
  user-select: none;
  border: 1px solid $line-color;
  .modal-tb-header {
    @extend %flex;
    cursor: pointer;
    position: relative;
    width: 100%;
    padding: 0.4rem 0.75rem 0.5rem;
    border-bottom: 1px solid $line-color;
    .modal-tb-header-left {
      font-weight: 600;
      color: $header-font-color;
    }
    .modal-tb-header-right-asc,
    .modal-tb-header-right-desc {
      position: absolute;
      top: 0.3rem;
      opacity: 0.4;
    }
    .modal-tb-header-right-asc {
      right: 1rem;
    }
    .modal-tb-header-right-desc {
      right: 0.5rem;
    }
    .active {
      opacity: 1;
    }
  }

  .table-body-wrap {
    width: 100%;
    overflow: auto;
    .table {
      margin: 0;
      td {
        padding: 0.35rem 0.75rem 0.4rem;
        border: none;
        cursor: pointer;
      }
      .active {
        background-color: $selected-color;
      }
    }
  }

  .b-table-head {
    th {
      padding: 0;
      overflow: hidden;
      border: 0 !important;
    }
  }
}

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

.tb {
  display: flex;
  flex-flow: column nowrap;
  flex: 1 1 auto;
  height: 100%;
  user-select: none;

  .tb-header {
    @extend %flex;
    border: 1px solid $line-color;
    padding: 0.8rem 0.3rem 0.8rem 0.65rem;

    .tb-header-left,
    .tb-header-right {
      cursor: pointer;
      @extend %flex;
    }

    .tb-header-left {
      width: 140px;
    }

    .tb-header-title {
      font-weight: 600;
      color: $header-font-color;
    }

    .tb-header-sort-arrows {
      position: relative;
      width: 1.5rem;

      .tb-header-sort-desc,
      .tb-header-sort-asc {
        position: absolute;
        margin: 0;
        padding: 0;
        opacity: 0.4;
        top: 0;
      }

      .tb-header-sort-desc {
        right: 8px;
      }

      .tb-header-sort-asc {
        right: 0px;
      }

      .active {
        opacity: 1;
      }
    }
  }

  .tb-body {
    overflow: auto;
    height: calc(100% - 70px);

    .tb-body-element {
      @extend %flex;
      border: 1px solid $line-color;
      border-bottom: none;
      padding: 0.8rem 0.3rem 0.8rem 0.65rem;
      cursor: pointer;
      line-height: 1.2rem;
      &:hover {
        background-color: $hover-color;
      }

      &:last-child {
        border-bottom: 1px solid $line-color;
      }

      .tb-body-element-right {
        display: flex;
        min-width: 4rem;
        justify-content: center;
        align-items: center;
      }
    }

    .active {
      background-color: $selected-color;

      &:hover {
        background-color: $selected-color;
      }
    }
  }
}
</style>

<script>
import store from "../../store";

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
    }
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
          check: this.status
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
