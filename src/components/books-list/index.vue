<template>
  <section>
      <h6 v-if="! selectedItem.id">Нет данных для отображения</h6>
      {{ selectedItem.id  }} <br/>
      {{ selectedItem.author }}
  </section>
</template>

<script>
export default {
  name: 'books-list',
  props: ["selectedItem"],
  data: function() {
    return {};
  }
};
</script>