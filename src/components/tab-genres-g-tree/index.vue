<template>
  <div id="tbs" class="tb">
    <div class="tb-header">
      <div class="tb-header-left" @click="sortText">
        <div class="tb-header-title">Серия</div>
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
    <div class="tb-body">
        <v-tree-select :data="data" value-field-name="id" v-model="selectItem"></v-tree-select>
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
import VTreeselect from 'vue-treeselect'
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
  components: {
    VTreeselect
  },
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
      data: [
            {
              "id": 1,
              "text": "Same but with checkboxes",
              "children": [
                {
                  "id": 2,
                  "text": "initially selected",
                },
                {
                  "id": 3,
                  "text": "custom icon",
                },
                {
                  "id": 4,
                  "text": "initially open",
                  "children": [
                    {
                      "id": 5,
                      "text": "Another node"
                    }
                  ]
                },
                {
                  "id": 6,
                  "text": "custom icon",
                },
                {
                  "id": 7,
                  "text": "disabled node",
                  "disabled": true
                }
              ]
            },
            {
              "id": 8,
              "text": "Same but with checkboxes",
              "children": [
                {
                  "id": 9,
                  "text": "initially selected",
                },
                {
                  "id": 10,
                  "text": "custom icon",
                },
                {
                  "id": 11,
                  "text": "initially open",
                  "children": [
                    {
                      "id": 12,
                      "text": "Another node"
                    }
                  ]
                },
                {
                  "id": 13,
                  "text": "custom icon",
                },
                {
                  "id": 14,
                  "text": "disabled node",
                  "disabled": true
                }
              ]
            },
            {
              "id": 15,
              "text": "And wholerow selection"
            }
          ],
          selectItem: null
      
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