<template>
  <div class="list-element"><img :src="icon"> {{ listItem.name }}</div>
</template>

<style lang="scss">
   .list-element {
     padding: .1rem .8rem .1rem .4rem;
      &:hover {
        background-color: #eee;
      }
     &>img{
       margin-top: -5px;
     } 
   }
</style>

<script>
export default {
  name: 'items-list',
  props: ['listItem'],
  computed: {
    icon: function () {
      let value = '';
      let path = this.$store.getters.prefix + "/static/assets/";
      switch (this.listItem.status) {
            case "add":
              value = path + 'add.png';
              break;
            case "raw":
              value = '';
              break;
            case "err":
              value = '';
              break;
          }
      return value
    }
  },
  data: function() {
    return {
      
    };
  }
  
};
</script>