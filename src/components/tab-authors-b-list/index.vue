<template>
  <section>
      <h6 v-if="this.$store.getters.authorID == -1">Нет данных для отображения</h6>
      <div v-else>
        {{ this.$store.getters.authorID }}
      </div>
  </section>
</template>

<script>
import store from "../../store";
export default {
  name: 'books-list',
  
  data: function() {
      return { };
  }
};
</script>