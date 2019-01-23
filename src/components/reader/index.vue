<template>
<div>
  reader
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


</style>

<script>
import store from "../../store";

export default {
  name: "reader",
  
  data: function() {
    return {
      
    };
  },
  beforeCreate: function() {
    store.commit("setReader", true);
  },
};
</script>