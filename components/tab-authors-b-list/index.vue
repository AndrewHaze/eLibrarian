<template>
  <section>
    <h6 v-if="this.curAI == -1">Нет данных для отображения</h6>
    <div v-else>
      <div class="cover-book-list" v-if="look === 'cover'">
        <div
          class="series-wrap"
          v-for="sItem in sListItems"
          :key="sItem.id"
          :class="{ shift: isShift }"
        >
          <div class="series-title" v-if="sItem.seriesTitle != 'Ђ'">
            <span>{{ sItem.seriesTitle }}</span>
          </div>
          <div
            class="item"
            v-for="bItem in bListItems"
            :id="bItem.id"
            :key="bItem.id"
            v-if="bItem.seriesTitle == sItem.seriesTitle"
            :class="{active: bItem.isActive}"
            @click="itemClickHandler"
          >
            <div class="cover">
              <img :src="'data:image/jpg;base64,'+bItem.cover">
            </div>
            <div class="info">
              <div class="book-authors">{{ bItem.author }}</div>
              <div class="book-title">{{ bItem.title }}</div>
              <div>{{ bItem.genres }}</div>
            </div>
          </div>
        </div>
      </div>
      <!---------------------------------------------------------------------------------------------->
      <div class="tree-book-list" v-else-if="look === 'tree'">B</div>
      <!---------------------------------------------------------------------------------------------->
      <div class="table-book-list" v-else-if="look === 'table'">
        <div class="tbl-table">
          <div class="tbl-header" :class="{ thPad: isPad }">
            <div class="tbl-table-row">
              <div class="tbl-table-cell cell-1">
                <font-awesome-icon icon="check"/>
              </div>
              <div class="tbl-table-cell cell-2">
                <font-awesome-icon icon="bell"/>
              </div>
              <div class="tbl-table-cell cell-3">
                <font-awesome-icon icon="heart"/>
              </div>
              <div class="tbl-table-cell cell-4">Название</div>
              <div class="tbl-table-cell cell-5">Серия</div>
              <div class="tbl-table-cell cell-6">№</div>
              <div class="tbl-table-cell cell-7">Жанр</div>
            </div>
          </div>
          <div id="table-body" class="tbl-table-body">
            <div
              class="tbl-table-row"
              v-for="bItem in bListItems"
              :key="bItem.id"
              :id="bItem.id"
              :class="{active: bItem.isActive}"
              @click="itemClickHandler"
              @mouseover="mouseOver"
              @mouseleave="mouseOver"
            >
              <div class="tbl-table-cell cell-1"></div>
              <div class="tbl-table-cell cell-2"></div>
              <div class="tbl-table-cell cell-3"></div>
              <div class="tbl-table-cell cell-4">{{ bItem.title }}</div>
              <div class="tbl-table-cell cell-5">
                <span v-if="bItem.seriesTitle != 'Ђ'">{{ bItem.seriesTitle }}</span>
              </div>
              <div class="tbl-table-cell cell-6">
                <span v-if="bItem.seriesTitle != 'Ђ'">{{ bItem.seriesNumber }}</span>
              </div>
              <div class="tbl-table-cell cell-7">{{ bItem.genres }}</div>
            </div>
          </div>
        </div>
      </div>
      <div
        v-show="bMenu"
        class="book-menu"
        :style="{ right: bMenuX +'px', top: bMenuY + 'px' }"
      >{{bMenuY}}</div>
    </div>
  </section>
</template>

<style lang="scss">
$line-color: #dee2e6;
$header-font-color: #495057;
$selected-color: #ddd;
$hover-color: rgba(221, 221, 221, 0.4);
$item-mr: 0.5rem;
$item-pd: 0.5rem;

.content section {
  margin-left: -0.5rem;
  overflow: hidden;
}

.content section > div {
  display: block;
  overflow: hidden;
  padding-bottom: 1.2rem;
  height: 100%;
  position: relative;
}

.book-menu {
  position: absolute;
  display: block;
  width: 100px;
  height: 100px !important;
  background-color: #fff;
  border: 1px solid #000;
  right: 0;
  top: 0;
}

.cover-book-list {
  display: block;
  height: 100%;
  padding-left: 0.2rem;
  overflow-y: auto;
  overflow-x: hidden;
  div {
    display: flex;
    user-select: none;
    cursor: pointer;
  }

  .series-wrap {
    flex-flow: row wrap;
    .series-title {
      position: relative;
      font-size: 1.3rem;
      font-weight: 600;
      line-height: 1.2;
      min-width: 100%;
      justify-content: center;
      margin: 1rem 0.5rem 0 0.25rem;

      &:first-child {
        margin-top: -0.3rem;
      }

      &::before {
        position: absolute;
        content: "";
        border-bottom: 1px solid $line-color;
        left: 0;
        right: 0.5rem;
        top: 0.85rem;
      }

      span {
        z-index: 1;
        background-color: #fff;
        padding: 0 0.5rem;
      }
    }

    .series-wrap + .series-wrap {
      border-bottom: 1px solid $line-color;
    }

    .item {
      flex-flow: row nowrap;
      width: 389px;
      margin: $item-mr;
      padding: $item-pd;
      transition: background-color 0.2s;
      overflow: hidden;

      &:hover {
        background-color: rgba(221, 221, 221, 0.4);
        transition: background-color 0.2s;
      }

      .cover {
        width: 160px;
        min-width: 160px;
        height: 220px;
        margin-right: 1rem;
        justify-content: center;
        overflow: hidden;

        > img {
          height: 100%;
        }
      }

      .info {
        flex-flow: column nowrap;
        line-height: 1.3;

        .book-authors {
          color: #4c4c4c;
        }

        .book-title {
          font-size: 1.2rem;
          font-weight: 600;
          margin: 0.3rem 0 0.3rem;
          line-height: 1.1;
        }
      }
    }
  }

  .shift {
    margin-top: -($item-mr + $item-pd);
    margin-bottom: 1rem;
  }
}

.table-book-list {
  padding-left: 0.5rem;
  display: block;
  height: 100%;
  div {
    user-select: none;
    cursor: pointer;
  }
  .tbl-table {
    display: block;
    height: 100%;
    .tbl-header {
      border-top: 1px solid $line-color;
      border-bottom: 1px solid $line-color;
      font-weight: 600;
      color: $header-font-color;
      .tbl-table-row {
        border: none;
        .tbl-table-cell {
          padding: 0.8rem 0.5rem;
        }
      }
    }

    .thPad {
      padding-right: 1rem;
    }

    .tbl-table-body {
      display: block;
      height: calc(100% - 50px);
      overflow-y: auto;
      .tbl-table-row {
        &:hover {
          background-color: $hover-color;
          transition: background-color 0.2s;
        }
      }
    }

    .tbl-table-row {
      display: flex;
      flex-flow: row nowrap;
      width: 100%;
      &:last-child {
        border-bottom: 1px solid $line-color;
      }

      .tbl-table-cell {
        display: flex;
        padding: 0.5rem;
        align-items: center;
        overflow: hidden;
      }
      .cell-1,
      .cell-2,
      .cell-3,
      .cell-6 {
        justify-content: center;
      }
      .cell-1 {
        width: 2rem;
      }
      .cell-2 {
        width: 2rem;
      }
      .cell-3 {
        width: 2rem;
      }
      .cell-4 {
        width: calc(100% - 50% - 9rem);
      }
      .cell-5 {
        width: 25%;
      }
      .cell-6 {
        width: 3rem;
      }
      .cell-7 {
        width: 25%;
      }
    }

    .tbl-table-row + .tbl-table-row {
      border-top: 1px solid $line-color;
    }
  }
}

.content {
  .active {
    background-color: $selected-color;
    transition: background-color 0.2s;
    &:hover {
      background-color: $selected-color !important;
      transition: background-color 0.2s;
    }
  }
}
</style>

<script>
import store from "../../store";

export default {
  name: "books-list",
  props: ["curAI"],
  data: function() {
    return {
      sTitle: "",
      //список серий
      sListItems: [],
      //список книг
      bListItems: [],
      isPad: false,
      bMenu: false,
      bMenuX: 200,
      bMenuY: 0
    };
  },
  watch: {
    curAI: function(val) {
      const self = this;
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "as_list",
          dat: val
        },
        "",
        function(rd) {
          self.sListItems = rd; //возвр. данные (Responce)
        }
      );
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "ab_list",
          dat: val
        },
        "",
        function(rd) {
          self.bListItems = rd; //возвр. данные (Responce)
        }
      );
    },
    bListItems: function() {
      this.setTableHeaderPad();
    }
  },
  computed: {
    isShift: function() {
      if (this.sListItems[0].seriesTitle == "Ђ") {
        return true;
      } else {
        return false;
      }
    },
    //стиль отображения
    look: function() {
      this.setTableHeaderPad();
      return store.getters.blLook;
    }
  },
  methods: {
    /* добавляем отступ в заголовок таблицы <tbl-table> 
       при появленни скрола у <table-body> 
       Скролл может появится:
          - при изменении массива bListItems (watch: bListItems);
          - при маштабировании окна (хук: resize);
          - при смене вида отображения (computed: look).
    */
    setTableHeaderPad() {
      this.$nextTick(function() {
        let el = document.getElementById("table-body");
        if (el) {
          let hSum = 0;
          for (let i = 0; i < el.children.length; i++) {
            hSum += el.children[i].clientHeight;
          }
          this.isPad = hSum > el.clientHeight;
        }
      });
    },
    handleResize() {
      this.setTableHeaderPad();
    },
    itemClickHandler(item) {
      //сбросим атрибуты по всему массиву
      this.bListItems.forEach(function(entry) {
        entry.isActive = false;
      });
      let element = this.bListItems[
        this.bListItems.map(el => el.id).indexOf(item.currentTarget.id)
      ];
      //store.commit("setAuthorID", element.id.substr(2));
      element.isActive = true;
      this.bMenu = element.isActive
    },
    mouseOver(item) {
      let element = this.bListItems[
        this.bListItems.map(el => el.id).indexOf(item.currentTarget.id)
      ];
      this.bMenu = element.isActive || false;
    }
  },
  mounted: function() {
    window.addEventListener("resize", this.handleResize);
  },
  beforeDestroy: function() {
    window.removeEventListener("resize", this.handleResize);
  }
};
</script>
