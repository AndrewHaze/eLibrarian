<template>
<section>
    <h6 v-if="this.curAI == -1">Нет данных для отображения</h6>
    <div v-else>
        <div v-for="item in bListItems" :key="item.id">
            {{ item.title }}
        </div>
    </div>
</section>
</template>

<script>
export default {
    name: 'books-list',
    props: ["curAI"],
    data: function () {
        return {
            bListItems: [],
        };
    },
    watch: {
        curAI: function (val) {
            const self = this;
            this.callApi(
                this.$store.getters.prefix + "/static/api.php", {
                    cmd: "b_list",
                    dat: val
                },
                "",
                function (rd) {
                    self.bListItems = rd; //возвр. данные (Responce)
                }
            );
        }
    }
};
</script>
