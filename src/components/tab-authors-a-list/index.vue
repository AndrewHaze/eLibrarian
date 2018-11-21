<template>
  <div class="tb">
    <div class="tb-header">
      <div class="tb-header-left">
        <div class="tb-header-title">Авторы</div>
        <div class="tb-header-sort-arrows">
          <span class="tb-header-sort-desc">&#8593;</span>
          <span class="tb-header-sort-asc">&#8595;</span>
        </div>
      </div>
      <div class="tb-header-right">
        <div class="tb-header-title">Книг</div>
        <div class="tb-header-sort-arrows">
          <span class="tb-header-sort-desc">&#8593;</span>
          <span class="tb-header-sort-asc">&#8595;</span>
        </div>
      </div>
    </div>
    <div class="tb-body">
      <div class="tb-body-element" v-for="item in aItems" :key="item.id" v-if="filter == '*'">
        <div class="tb-body-element-left">{{ item.author }}</div>
        <div class="tb-body-element-right">{{ item.books }}</div>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
%flex {
  display: flex;
  flex: row, nowrap;
  justify-content: space-between;
}

.tb {
  border: 1px solid #dee2e6;
  .tb-header {
    @extend %flex;
    border-bottom: 1px solid #dee2e6;
    padding: 0.8rem 0.3rem 0.8rem 0.65rem;
    .tb-header-left,
    .tb-header-right {
      @extend %flex;
    }
    .tb-header-left {
      width: 140px;
    }
    .tb-header-title {
      font-weight: bold;
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
      .active {
        opacity: 1;
      }
      .tb-header-sort-desc {
        right: 8px;
      }
      .tb-header-sort-asc {
        right: 0px;
      }
    }
  }

  .tb-body-element {
    @extend %flex;
    border-top: 1px solid #dee2e6;
    padding: 0.8rem 0.3rem 0.8rem 0.65rem;
    .tb-body-element-right {
      width: 4rem;
      text-align: center;
    }
  }
}
</style>

<script>
export default {
  name: "items-list",
  props: ["aItems"],
  data: function() {
    return {
      filter: 'g',
    };
  }
};
</script>