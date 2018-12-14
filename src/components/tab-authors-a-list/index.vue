<template>
  <div id="tb" class="tb">
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
          <b-btn variant="warning" title="Сделать синонимом...">
            <font-awesome-icon icon="user-friends"/>
          </b-btn>
          <b-btn variant="warning" title="Объединить автора с...">
            <font-awesome-icon icon="user-minus"/>
          </b-btn>
        </b-button-group>
        <b-button-group class="mx-1" size="sm">
          <b-btn variant="danger" title="Удалить Автора">
            <font-awesome-icon icon="user-times"/>
          </b-btn>
        </b-button-group>
      </b-button-toolbar>
    </div>
  </transition>
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

export default {
  name: "items-list",
  props: ["aItems", "aFilter"],
  data: function() {
    return {
      aAsc: true,
      aDesc: false,
      bAsc: false,
      bDesc: false,
      //
      aMenu: false,
      aMenuX: 0,
      aMenuY: 0
    };
  },
  methods: {
    itemClickHandler(item) {
      //сбросим атрибуты по всему массиву
      this.aItems.forEach(function(entry) {
        entry.isActive = false;
      });
      let element = this.aItems[
        this.aItems.map(el => el.id).indexOf(item.currentTarget.id)
      ];
      store.commit("setAuthorID", element.id.substr(2));
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
        let p = document.getElementById("tb").getBoundingClientRect();
        //координаты относительно родителя
        let c = document.getElementById(id).getBoundingClientRect();
        this.aMenuX = c.left + (c.width - mW) / 2 - p.left;
        if (this.getViewportHeight() > c.bottom + p.top - mH*2.5) {
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
