<template>
  <b-modal
    id="bookEditor"
    ref="modal-e"
    @shown
    @hidden
    no-close-on-backdrop
    hide-footer
    size="max"
    title="Книга"
  >
    <b-container fluid :style="{ maxHeight: mHeight + 'px', minHeight: mHeight + 'px' }">
      <b-row>
        <b-col sm="2">1</b-col>
        <b-col sm="10">
          <b-container fluid class="px-0">
            <div class="colappse-header"><span>Книга</span>
              <b-btn
                @click="showCollapse1 = !showCollapse1"
                :class="showCollapse1 ? 'collapsed' : null"
                aria-controls="collapse1"
                :aria-expanded="showCollapse1 ? 'true' : 'false'"
                variant="outline-secondary"
                class="btn-circle"
              >
                <font-awesome-icon icon="chevron-up" v-if="showCollapse1"/>
                <font-awesome-icon icon="chevron-down" v-if="!showCollapse1"/>
              </b-btn>
            </div>
            <b-collapse id="collapse1" class="mt-2" v-model="showCollapse1">
              <p class="card-text">Collapse contents Here</p>
            </b-collapse>
          </b-container>
        </b-col>
      </b-row>
    </b-container>
  </b-modal>
</template>

<style lang="scss">
$line-color: #dee2e6;
$header-font-color: #495057;
$selected-color: #ddd;
$hover-color: rgba(221, 221, 221, 0.4);
$header-bk-color: #abafb4;
.colappse-header {
  display: flex;
  flex-flow: row nowrap;
  justify-content: space-between;
  position: relative;
  &::before {
    position: absolute;
    content: "";
    border-bottom: 1px solid #dee2e6;
    left: 0;
    right: 1.6rem;
    top: 0.95rem;
  }
  span {
      padding-right: .3rem;
      background-color: #fff;
      z-index: 1;
  }
  .btn-circle {
    width: 20px;
    height: 20px;
    text-align: center;
    padding: 4px 0;
    font-size: 12px;
    line-height: 0;
    border-radius: 10px;
    margin-top: 5px;
    &:focus {
      box-shadow: none;
    }
  }
}
</style>

<script>
import axios from "axios";
import store from "../../store";

const shiftL = 170;

export default {
  name: "modal-be",
  data() {
    return {
      mHeight: 100,
      pHeight: 100,
      showCollapse1: true
    };
  },
  mounted: function() {
    window.addEventListener("resize", this.handleResize);
    this.mHeight = window.innerHeight - shiftL;
    if (this.mHeight > 1200) {
      this.mHeight = 1200;
    }
  },
  beforeDestroy: function() {
    window.removeEventListener("resize", this.handleResize);
  }
};
</script>
