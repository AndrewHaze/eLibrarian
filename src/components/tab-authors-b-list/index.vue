<template>
  <section>
    <h6 v-if="this.curAI == -1">Нет данных для отображения</h6>
    <div v-else>
      <div class="cover-book-list">
        <div class="series-wrap" v-for="sItem in sListItems" :key="sItem.id">
          <div class="series-title" v-if="sItem.seriesTitle != 'Ђ'"><span>{{ sItem.seriesTitle }}</span></div>
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
    </div>
  </section>
</template>

<style lang="scss">
$line-color: #dee2e6;
.content section {
  margin-left: -0.5rem;
}
.cover-book-list {
  display: flex;
  div {
    display: flex;
  }
  .series-wrap {
    flex-flow: row wrap;
    flex: 1 1 auto;
    margin-bottom: 2rem;
    .series-title {
      position: relative;
      font-size: 1.3rem;
      font-weight: 600;
      line-height: 1.2;
      min-width: 100%;
      justify-content: center;
      margin: 1rem 0.5rem 1.2rem 0.5rem;
      &:first-child {
        margin-top: -0.3rem;
      }
      &::before {
        position: absolute;
        content: '';
        
        border-bottom: 1px solid $line-color;
        left: 0;
        right: .5rem;
        top: .85rem;
      }
      span {
        z-index: 1;
        background-color: #fff;
        padding: 0 .5rem;
      }
    }
  }

  .series-wrap + .series-wrap {
      border-bottom: 1px solid $line-color;
  }

  .item {
    flex-flow: row nowrap;
    width: 23.5rem;
    margin-bottom: 2rem;
    padding: 0.3rem;
    &:first-child {
      margin-left: 1.5rem;
    }
    &:hover {
      background-color: $line-color;
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
        margin-top: 0.3rem;
      }
    }
  }
  .item + .item {
    margin-left: 1.5rem;
  }
}
</style>
    
<script>
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
  }
};
</script>
