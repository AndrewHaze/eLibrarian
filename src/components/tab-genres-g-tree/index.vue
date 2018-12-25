<template>
  <div id="tbg" class="tbg">
    <div class="tb-header">
      <div class="tb-header-left" @click="sortText">
        <div class="tb-header-title">Жанры</div>
        <div class="tb-header-sort-arrows">
          <span class="tb-header-sort-desc" :class="{ active: gDesc }">&#8593;</span>
          <span class="tb-header-sort-asc" :class="{ active: gAsc }">&#8595;</span>
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
    <div class="tb-body">
      <v-jstree
        ref="tree"
        :data="gItems"
        whole-row
        size="large"
        klass="genres-tree"
        @item-click="itemClick">
        <template slot-scope="_">
          <div class="tree-item">
            <span>{{ _.model.text }}</span>
            <span>{{ _.model.count }}</span>
          </div>
        </template>
      </v-jstree>
    </div>
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

.tbg {
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
      width: 360px;
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

    .genres-tree {
      //padding: .5rem 0;
      li {
        width: auto;
      }
      .tree-icon {
        float: left;
      }
      .tree-anchor {
        display: flex;
        flex: 1 1 auto;
        width: calc(100% - 45px);
      }

      .tree-item {
        @extend %flex;
        font-size: 1rem;
        width: 100%;
      }
      
      .tree-wholerow-hovered {
        background-color: $hover-color;
      }
      .tree-wholerow-clicked {
        background-color: $selected-color;
      }
       
      .tree-disabled {
        opacity: .5;
      }

      
    }
  }
}
</style>

<script>
import store from "../../store";

export default {
  name: "items-list",
  props: ["gItems"],
  data: function() {
    return {
      status: "no",
      gAsc: true,
      gDesc: false,
      bAsc: false,
      bDesc: false,
      orderCode: 0
    };
  },
  watch: {
    gItems: function() {
      this.$refs.tree.initializeData(this.gItems);
    }
  },
  methods: {
    itemClick(node) {
      console.log(node.model.text + " clicked !");
    },
    sortText() {
      this.bAsc = false;
      this.bDesc = false;
      if (this.gAsc) {
        this.gDesc = true;
        this.gAsc = false;
      } else if (this.gDesc) {
        this.gDesc = false;
        this.gAsc = true;
      } else {
        this.gDesc = false;
        this.gAsc = true;
      }
      this.orderCode = this.gDesc ? 1 : 0;
      store.commit("setOrderCode", this.orderCode);
    },
    sortNumeric() {
      this.gAsc = false;
      this.gDesc = false;
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
      this.orderCode = this.bDesc ? 3 : 2;
      store.commit("setOrderCode", this.orderCode);
    }
  },
  //Хук <UPDATED> вызывается после изменения данных в компоненте и перерисовки DOM
  updated: function() {
    // if (store.getters.seriesID === -1) {
    //   this.sItems.forEach(function(entry) {
    //     entry.isActive = false;
    //   });
    // }
  }
};
</script>