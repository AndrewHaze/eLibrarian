<template>
  <div class="list-element" :title="title"><img :src="icon"> {{ listItem.text }}</div>
</template>

<style lang="scss">
.list-element {
  padding: 0.1rem 0.8rem 0.1rem 0.4rem;
  &:hover {
    background-color: #eee;
  }
  & > img {
    margin-top: -5px;
  }
}
</style>

<script>
export default {
  name: "items-list",
  props: ["listItem"],
  computed: {
    icon: function() {
      let value = "";
      let path = this.$store.getters.prefix + "/static/assets/";
      switch (this.listItem.status) {
        case "add":
          value = path + "add.png";
          break;
        case "err":
          value = path + "err.png";
          break;
        case "ndf":
          value = path + "ndf.png";
          break;
        case "dma":
          value = path + "dma.png";
          break;
        case "per":
          value = path + "per.png";
          break;
        case "dbe":
          value = path + "dbe.png";
          break;
      }
      return value;
    },
    title: function() {
      let value = "";
      switch (this.listItem.status) {
        case "add":
          value = "Файл успешно добавлен";
          break;
        case "err":
          value = "Ошибка чтения";
          break;
        case "ndf":
          value = "Требуется описание книги";
          break;
        case "dma":
          value = "Поврежденный архив";
          break;
        case "per":
          value = "Ошибка разбора";
          break;
        case "dbe":
          value = "Ошибка обновления БД";
          break;
      }
      return value;
    }
  },
  data: function() {
    return {};
  }
};
</script>