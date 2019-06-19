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
        @item-click="itemClick"
      >
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
$selected-color: #ddd;
$hover-color: rgba(221, 221, 221, 0.4);

%flex {
  display: flex;
  flex: row, nowrap;
  justify-content: space-between;
}

.genres-tree {
  border: 1px solid $line-color;

  .tree-container-ul {
    margin-bottom: -6px;
  }

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
    opacity: 0.5;
  }
}
</style>

<script>
import store from "../../../../store";

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
      /* 
         gId — отличительный признак для одинаковых id 
         у разных узлов дерева, для срабатывания wath,
         после срабатывания дробь отбрасываем 
      */
     console.log(node)
      let gId = (node.model.type === 'leaf') ? 0 : .1;
      store.commit("setGenresID", node.model.id + gId);
      /////////////////////////////////////////////////
      store.commit("setGenresType", node.model.type);
      store.commit("setGenresTitle", node.model.text);
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
    updated: function() {

      let element = this.gItems[ //вост. подсветку автора после перерерисовки
        this.gItems.map(el => el.value).indexOf(store.getters.genresTitle)
      ];
      if (element) {
        element.selected = true;
      } else {
          store.commit("setGenresID", -1);
      }
  }
};
</script>