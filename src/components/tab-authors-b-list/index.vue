<template>
<section>
    <h6 v-if="this.curAI == -1">Нет данных для отображения</h6>
    <div v-else>
      <div class="cover-book-list" >
          <div class="item" v-for="item in bListItems" :key="item.id">
              <div class = "cover">
                <img :src="'data:image/jpg;base64,'+item.cover">
              </div>
              <div class = "info">
                <div class="book-authors">{{ item.author }} </div>
                <div class="book-title">{{ item.title }}</div>
              </div>
          </div>
      </div>
    </div>
</section>
</template>

<style lang="scss">
.content  section {
  margin-left: -.5rem;
}
.cover-book-list {
  display: flex;
  flex-flow: row wrap;
  div {
    display: flex;
  }
  .item {
    flex-flow: row nowrap;
    width: 23.5rem;
    margin-bottom: 2rem;
    padding: .3rem;
    &:first-child {
      margin-left: 1.5rem;
    }
    &:hover {
      background-color: #eee;
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
        margin-top: .3rem;
      }
    }
  }
  .item+.item {
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
      bListItems: []
    };
  },
  watch: {
    curAI: function(val) {
      const self = this;
      this.callApi(
        this.$store.getters.prefix + "/static/api.php",
        {
          cmd: "b_list",
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
