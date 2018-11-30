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
            :key="bItem.id"
            v-if="bItem.seriesTitle == sItem.seriesTitle"
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
      <div v-else-if="look === 'tree'">B</div>
      <div v-else-if="look === 'table'">
        <div class v-for="bItem in bListItems" :key="bItem.id">
          <div>{{ bItem.title }}</div>
          <div>{{ bItem.genres }}</div>
          <div v-if="bItem.seriesTitle != 'Ђ'">{{ bItem.seriesTitle }}</div>
        </div>
      </div>
    </div>
  </section>
</template>

<style lang="scss">
$line-color: #dee2e6;
$item-mr: 0.5rem;
$item-pd: 0.5rem;

.content section {
  margin-left: -0.5rem;
}
.cover-book-list {
  display: flex;
  flex-flow: column nowrap;
  padding-left: 0.2rem;
  div {
    display: flex;
    user-select: none;
    cursor: pointer;
  }
  .series-wrap {
    flex-flow: row wrap;
    flex: 1 1 auto;
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
          font-weight: 550;
          margin: 0.3rem 0 0.3rem;
          line-height: 1.1;
        }
      }
    }
  }
  .shift {
    margin-top: -($item-mr + $item-pd);
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
      sListItems: [],
      bListItems: []
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
    }
  },
  computed: {
    isShift: function() {
      if (this.sListItems[0].seriesTitle === "Ђ") {
        return true;
      } else {
        return false;
      }
    },
    look: function() {
      return store.getters.blLook;
    }
  }
};
</script>
