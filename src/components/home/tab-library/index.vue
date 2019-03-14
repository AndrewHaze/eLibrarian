<template>
  <section>
    <b-row class="fix-height fix-height-tl pt-3">
      <b-col class="sidebar sidebar-lib ml-3 p-0" :style="{ maxHeight: mHeight + 'px', minHeight: mHeight + 'px' }">
        <div id="lib-navigation" class="lib-navigation">
          <div class="ln-header">Библиотека</div>
          <div class="ln-list">
            <div id="li1" class="ln-list-item" @click="itemClick(1)"><font-awesome-icon icon="clock" style="color: #069adc"/>Недавно открытые</div>
            <div id="li2" class="ln-list-item" @click="itemClick(2)"><font-awesome-icon icon="check" style="color: #30e52a"/>Прочитаные</div>
            <div id="li3" class="ln-list-item" @click="itemClick(3)"><font-awesome-icon icon="calendar-check" style="color: #ffa500"/>Запланированные</div>
            <div id="li4" class="ln-list-item" @click="itemClick(4)"><font-awesome-icon icon="heart" style="color: #c91212"/>Избранные</div>
          </div>
          <div class="ln-header">Новые поступления</div>
          <div class="ln-list">
            <div id="li5" class="ln-list-item" @click="itemClick(5)"><font-awesome-icon icon="calendar-week" style="color: #00bcd4"/>За неделю</div>
            <div id="li6" class="ln-list-item" @click="itemClick(6)"><font-awesome-icon icon="calendar-alt" style="color: #00bcd4"/>За месяц</div>
            <div id="li7" class="ln-list-item" @click="itemClick(7)"><font-awesome-icon icon="calendar" style="color: #00bcd4"/>За год</div>
            <div id="li8" class="ln-list-item" @click="itemClick(8)"><font-awesome-icon icon="calendar-day" style="color: #00bcd4"/>За период</div>
          </div>
        </div>
      </b-col>
      <b-col class="content">
        <books-list :sid="sid" :curSLI="currentSLI"></books-list>
      </b-col>
    </b-row>
  </section>
</template>

<script>
import BooksList from "../../lib/books-list";
import store from "../../../store";

const shiftL = 210;

export default {
  name: "tab-library",
  components: {
    BooksList
  },
  data: function() {
    return {
      info: null,
      loading: true,
      errored: false,
      sid: "tld",
      mHeight: 100,
    };
  },
  mounted: function() {
    this.getLibraryS();
    
    window.addEventListener("resize", this.handleResize);
    this.mHeight = window.innerHeight - shiftL;
    if (this.mHeight > 1200) {
      this.mHeight = 1200;
    }
  },
  beforeDestroy: function() {
    window.removeEventListener("resize", this.handleResize);
  },
  computed: {
    currentSLI: function() {
      return this.$store.getters.librarySID;
    }
  },

  methods: {
    getLibraryS() {
      let elements = document.querySelectorAll('#lib-navigation .active');  
      if (elements[0]) {
        elements[0].classList.remove('active');
      }
      store.commit("setLibrarySID", -1);
    },
    handleResize() {
      this.mHeight = window.innerHeight - shiftL;
      if (this.mHeight > 1200) {
        this.mHeight = 1200;
      }
    },
    itemClick(item) {
      this.getLibraryS();
      let elem = document.getElementById('li'+item);
      elem.classList.add('active');
      store.commit("setLibrarySID", item);
      
    }
  }
};
</script>

<style lang='scss'>
$line-color: #dee2e6;
$header-font-color: #495057;
$selected-color: #ddd;
$hover-color: rgba(221, 221, 221, 0.4);

.fix-height-tl {
  height: calc(100vh - 174px);
}


.sidebar-lib {
  flex: 0 0 13.5rem !important;
  min-width: 13.5rem;
}
.lib-navigation {
  display: flex;
  flex-flow: column nowrap;
  border-left: 1px solid $line-color;
  border-right: 1px solid $line-color;
  border-bottom: 1px solid $line-color;
  user-select: none;
  overflow: auto;
  .ln-header {
    font-weight: 600;
    color: $header-font-color;
    padding: 0.5rem 0.3rem 0.5rem 0.65rem;
    border-top: 1px solid $line-color;
    border-bottom: 1px solid $line-color;
  }
  .ln-list {
    padding: 0;
    .ln-list-item {
      padding: 0.4rem 0.3rem 0.4rem 1.2rem;
      line-height: 1.2rem;
      cursor: pointer;
      &:hover {
        background-color: $hover-color;
      }
      &:last-child {
        padding-bottom: .5rem;
      }
      &.active {
        background-color: $selected-color;
      }
      svg {
        margin-right: 0.7rem;
      }
    }
  }
}
</style>
