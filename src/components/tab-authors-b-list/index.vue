<template>
<section>
    <h6 v-if="this.curAI == -1">Нет данных для отображения</h6>
    <div id="tad" v-else>
        <div class="cover-book-list" v-if="look === 'cover'" @scroll="onScroll">
            <div class="series-wrap" v-for="sItem in sListItems" :key="sItem.id">
                <div class="series-title" v-if="sItem.seriesTitle === 'яяяяяя'">
                    <span>Без серии</span>
                </div>
                <div class="series-title" v-else>
                    <span>{{ sItem.seriesTitle }}</span>
                </div>
                <div class="item" v-for="bItem in bListItems" :id="bItem.id" :key="bItem.id" v-if="bItem.seriesTitle == sItem.seriesTitle" :class="{active: bItem.isActive}" @click="itemClickHandler" @mouseover="mouseOverBook" @mouseleave="mouseLeaveBook">
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
            <!--******************************************************************************************-->
            <div class="tree-book-list" v-else-if="look === 'tree'">B</div>
            <!--******************************************************************************************-->
            <div class="table-book-list" v-else-if="look === 'table'">
                <div class="tbl-table">
                    <div class="tbl-header" :class="{ thPad: isPad }">
                        <div class="tbl-table-row">
                            <div class="tbl-table-cell cell-1">
                                <font-awesome-icon icon="check" />
                            </div>
                            <div class="tbl-table-cell cell-2">
                                <font-awesome-icon icon="calendar-check" />
                            </div>
                            <div class="tbl-table-cell cell-3">
                                <font-awesome-icon icon="heart" />
                            </div>
                            <div class="tbl-table-cell cell-4">Название</div>
                            <div class="tbl-table-cell cell-5">Серия</div>
                            <div class="tbl-table-cell cell-6">№</div>
                            <div class="tbl-table-cell cell-7">Жанр</div>
                            <div class="tbl-table-cell cell-8">Рейтинг</div>
                        </div>
                    </div>
                    <div id="table-body" class="tbl-table-body" @scroll="onScroll">
                        <div class="tbl-table-row" v-for="bItem in bListItems" :key="bItem.id" :id="bItem.id" :class="{active: bItem.isActive}" @click="itemClickHandler" @mouseover="mouseOverBook" @mouseleave="mouseLeaveBook">
                            <div class="tbl-table-cell cell-1">
                                <font-awesome-icon v-if="bItem.isRead" icon="check" style="color: #30e52a" />
                            </div>
                            <div class="tbl-table-cell cell-2">
                                <font-awesome-icon v-if="bItem.isToPlan" icon="calendar-check" style="color: #ffa500" />
                            </div>
                            <div class="tbl-table-cell cell-3">
                                <font-awesome-icon v-if="bItem.isFavorites" icon="heart" style="color: #c91212" />
                            </div>
                            <div class="tbl-table-cell cell-4">{{ bItem.title }}</div>
                            <div class="tbl-table-cell cell-5">
                                <span v-if="bItem.seriesTitle != 'яяяяяя'">{{ bItem.seriesTitle }}</span>
                            </div>
                            <div class="tbl-table-cell cell-6">
                                <span v-if="bItem.seriesTitle != 'яяяяяя'">{{ bItem.seriesNumber }}</span>
                            </div>
                            <div class="tbl-table-cell cell-7">{{ bItem.genres }}</div>
                            <div class="tbl-table-cell cell-8">
                                <template v-for="n in 5">
                                    <font-awesome-icon icon="star" :class="{ star: (bItem.howManyStars >= n) }" />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <transition name="fade">
            <div v-show="bMenu" class="book-menu" :style="{ top: bMenuY + 'px', left: bMenuX + 'px' }" @mouseover="mouseOverBookMenu" @mouseleave="mouseLeaveBook">
                <b-button-toolbar key-nav aria-label="Toolbar with button groups">
                    <b-button-group class="mx-1" size="sm">
                        <b-btn variant="primary" title="Открыть книгу для чтения">
                            <font-awesome-icon icon="book-reader" />
                        </b-btn>
                        <b-btn variant="primary" title="Править информацию о книге">
                            <font-awesome-icon icon="edit" />
                        </b-btn>
                    </b-button-group>
                    <b-button-group class="mx-1" size="sm">
                        <b-btn variant="danger" title="Удалить книгу" v-b-modal.modal1>
                            <font-awesome-icon icon="trash-alt" />
                        </b-btn>
                    </b-button-group>
                    <b-button-group class="mx-1" size="sm">
                        <b-btn variant="success" title="Прочитано" :pressed="Boolean(isRead)" @click="readButtonClick">
                            <font-awesome-icon icon="check" />
                        </b-btn>
                        <b-btn variant="success" title="Запланировать" :pressed="Boolean(isToPlan)" @click="toPlanButtonClick">
                            <font-awesome-icon icon="calendar-check" />
                        </b-btn>
                        <b-btn variant="success" title="В избранное" :pressed="Boolean(isFavorites)" @click="favoritesButtonClick">
                            <font-awesome-icon icon="heart" />
                        </b-btn>
                    </b-button-group>
                    <b-dropdown class="mx-1" right size="sm" variant="warning" title="Оценить книгу">
                        <template slot="button-content">
                            <font-awesome-icon icon="star-half-alt" />
                        </template>
                        <b-dropdown-item class="star" @click="starsButton1Click">
                            <font-awesome-icon icon="star" />
                        </b-dropdown-item>
                        <b-dropdown-item class="star" @click="starsButton2Click">
                            <template v-for="n in 2">
                                <font-awesome-icon icon="star" />
                            </template>
                        </b-dropdown-item>
                        <b-dropdown-item class="star" @click="starsButton3Click">
                            <template v-for="n in 3">
                                <font-awesome-icon icon="star" />
                            </template>
                        </b-dropdown-item>
                        <b-dropdown-item class="star" @click="starsButton4Click">
                            <template v-for="n in 4">
                                <font-awesome-icon icon="star" />
                            </template>
                        </b-dropdown-item>
                        <b-dropdown-item class="star" @click="starsButton5Click">
                            <template v-for="n in 5">
                                <font-awesome-icon icon="star" />
                            </template>
                        </b-dropdown-item>
                        <b-dropdown-divider></b-dropdown-divider>
                        <b-dropdown-item @click="starsButton0Click">Очистить</b-dropdown-item>
                    </b-dropdown>
                </b-button-toolbar>
            </div>
        </transition>
        <b-modal id="modal1" size="lg" title="Удаление книги" @ok="delHandleOk" @shown="hideMenu" ok-title="Удалить" ok-variant="danger" cancel-title="Отмена">
            <h4>Вы действительно хотите удалить выбранную книгу из библиотеки?</h4>
        </b-modal>
</section>
</template>

<style lang="scss">
$line-color: #dee2e6;
$header-font-color: #495057;
$selected-color: #ddd;
$hover-color: rgba(221, 221, 221, 0.4);
$item-mr: 0.5rem;
$item-pd: 0.5rem;

.content section {
    margin-left: -0.5rem;
    overflow: hidden;
}

#tad {
    display: block;
    overflow: hidden;
    padding-bottom: 1.2rem;
    height: 100%;
}

.book-menu {
    position: absolute;
    display: flex;
    width: auto;
    height: auto !important;
    z-index: 10;
    top: 0;

    .btn:focus {
        box-shadow: none !important;
    }
}

.star {
    color: #ffd700;

    &:hover,
    &:focus {
        color: #ffd700;
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}

.cover-book-list {
    display: block;
    height: 100%;
    padding-left: 0.2rem;
    overflow-y: auto;
    overflow-x: hidden;

    div {
        display: flex;
        user-select: none;
    }

    .series-wrap {
        flex-flow: row wrap;

        .series-title {
            position: relative;
            font-size: 1.3rem;
            font-weight: 600;
            line-height: 1.2;
            min-width: 100%;
            justify-content: center;
            margin: 1rem 0.5rem 0 0.25rem;

            &:first-child {
                margin-top: -0.3rem;
            }

            &::before {
                position: absolute;
                content: "";
                border-bottom: 1px solid $line-color;
                left: 0;
                right: 0.5rem;
                top: 0.85rem;
            }

            span {
                z-index: 1;
                background-color: #fff;
                padding: 0 0.5rem;
            }
        }

        .series-wrap+.series-wrap {
            border-bottom: 1px solid $line-color;
        }

        .item {
            flex-flow: row nowrap;
            width: 389px;
            margin: $item-mr;
            padding: $item-pd;
            transition: background-color 0.2s;
            overflow: hidden;
            cursor: pointer;

            &:hover {
                background-color: rgba(221, 221, 221, 0.4);
                transition: background-color 0.2s;
            }

            .cover {
                width: 160px;
                min-width: 160px;
                height: 220px;
                margin-right: 1rem;
                justify-content: center;
                overflow: hidden;

                >img {
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
                    font-weight: 600;
                    margin: 0.3rem 0 0.3rem;
                    line-height: 1.1;
                }
            }
        }
    }
}

.table-book-list {
    padding-left: 0.5rem;
    display: block;
    height: 100%;

    div {
        user-select: none;
        cursor: pointer;
    }

    .tbl-table {
        display: block;
        height: 100%;

        .tbl-header {
            border-top: 1px solid $line-color;
            border-bottom: 1px solid $line-color;
            font-weight: 600;
            color: $header-font-color;

            .tbl-table-row {
                border: none;

                .tbl-table-cell {
                    padding: 0.8rem 0.5rem;
                }
            }
        }

        .thPad {
            padding-right: 1rem;
        }

        .tbl-table-body {
            display: block;
            height: calc(100% - 50px);
            overflow-y: auto;
            cursor: default;

            .tbl-table-row {
                &:hover {
                    background-color: $hover-color;
                    transition: background-color 0.2s;
                }

                .cell-8 {
                    color: #eee;
                    font-size: 0.8rem;
                }
            }
        }

        .tbl-table-row {
            display: flex;
            flex-flow: row nowrap;
            width: 100%;
            cursor: pointer;

            &:last-child {
                border-bottom: 1px solid $line-color;
            }

            .tbl-table-cell {
                display: flex;
                padding: 0.5rem;
                align-items: center;
                overflow: hidden;
            }

            .cell-1,
            .cell-2,
            .cell-3,
            .cell-6 {
                justify-content: center;
            }

            .cell-1 {
                width: 2rem;
            }

            .cell-2 {
                width: 2rem;
            }

            .cell-3 {
                width: 2rem;
            }

            .cell-4 {
                width: calc(100% - 50% - 14rem);
            }

            .cell-5 {
                width: 25%;
            }

            .cell-6 {
                width: 3rem;
            }

            .cell-7 {
                width: 25%;
            }

            .cell-8 {
                width: 5rem;
            }
        }

        .tbl-table-row+.tbl-table-row {
            border-top: 1px solid $line-color;
        }
    }
}

.content {
    .active {
        background-color: $selected-color;
        transition: background-color 0.2s;

        &:hover {
            background-color: $selected-color !important;
            transition: background-color 0.2s;
        }
    }
}
</style>

<script>
import store from "../../store";

export default {
    name: "books-list",
    props: ["curAI"],
    data: function () {
        return {
            sTitle: "",
            //список серий
            sListItems: [],
            //список книг
            bListItems: [],
            bookID: "",
            isRead: false,
            isToPlan: false,
            isFavorites: false,
            howManyStars: 0,
            selectedItem: null,
            //*********
            isPad: false,
            bMenu: false,
            bMenuX: 0,
            bMenuY: 0
        };
    },
    watch: {
        curAI: function (val) {
            const self = this;
            this.callApi(
                this.$store.getters.prefix + "/static/api.php", {
                    cmd: "as_list",
                    dat: val
                },
                "",
                function (rd) {
                    self.sListItems = rd; //возвр. данные (Responce)
                }
            );
            this.callApi(
                this.$store.getters.prefix + "/static/api.php", {
                    cmd: "ab_list",
                    dat: val
                },
                "",
                function (rd) {
                    self.bListItems = rd; //возвр. данные (Responce)
                }
            );
        },
        bListItems: function () {
            this.setTableHeaderPad();
        }
    },
    computed: {
        //стиль отображения
        look: function () {
            this.setTableHeaderPad();
            return store.getters.blLook;
        }
    },
    methods: {
        delHandleOk() {
            const self = this;
            this.callApi(
                this.$store.getters.prefix + "/static/api.php", {
                    cmd: "del_book",
                    dat: this.bookID
                },
                "",
                function (rd) {}
            );
        },
        hideMenu() {
            this.bMenu = false;
        },
        setTableHeaderPad() {
            /* добавляем отступ в заголовок таблицы <tbl-table> 
                                   при появленни скрола у <table-body> 
                                   Скролл может появится:
                                      - при изменении массива bListItems (watch: bListItems);
                                      - при маштабировании окна (хук: resize);
                                      - при смене вида отображения (computed: look).
                              */
            this.$nextTick(function () {
                let el = document.getElementById("table-body");
                if (el) {
                    let hSum = 0;
                    for (let i = 0; i < el.children.length; i++) {
                        hSum += el.children[i].clientHeight;
                    }
                    this.isPad = hSum > el.clientHeight;
                }
            });
        },
        handleResize() {
            this.setTableHeaderPad();
            this.bMenu = false;
        },
        //кандитат в миксины
        getViewportHeight(doc) {
            doc = doc || document;
            var elem =
                doc.compatMode == "CSS1Compat" ? doc.documentElement : doc.body;
            return elem.clientHeight;
        },
        menuPos(id) {
            if (this.bMenu) {
                //размеры меню
                let mW = 264,
                    mH = 32;
                //координаты родителя
                let p = document.getElementById("tad").getBoundingClientRect();
                //координаты относительно родителя
                let c = document.getElementById(id).getBoundingClientRect();
                this.bMenuX = c.left + (c.width - mW) / 2 - p.left;
                if (this.getViewportHeight() > c.bottom + p.top - mH) {
                    this.bMenuY = c.bottom - p.top - 3;
                } else {
                    this.bMenuY = c.top - p.top - mH + 3;
                }
            }
        },
        itemClickHandler(item) {
            //сбросим атрибуты по всему массиву
            this.bListItems.forEach(function (entry) {
                entry.isActive = false;
            });
            let element = this.bListItems[
                this.bListItems.map(el => el.id).indexOf(item.currentTarget.id)
            ];
            this.bookID = element.id.substr(2);
            element.isActive = true;
            this.selectedItem = element;
            this.bMenu = true;
            this.menuPos(item.currentTarget.id);
        },
        mouseOverBook(item) {
            let element = this.bListItems[
                this.bListItems.map(el => el.id).indexOf(item.currentTarget.id)
            ];
            this.isRead = element.isRead;
            this.isToPlan = element.isToPlan;
            this.isFavorites = element.isFavorites;
            this.howManyStars = element.isFavorites;
            this.bMenu = element.isActive || false;
            this.menuPos(item.currentTarget.id);
        },
        mouseLeaveBook(item) {
            this.bMenu = false;
        },
        mouseOverBookMenu(item) {
            this.bMenu = true;
        },
        readButtonClick(item) {
            this.bMenu = false;
            let element = this.bListItems[
                this.bListItems.map(el => el.id).indexOf(this.selectedItem.id)
            ];
            element.isRead = !element.isRead;
            const self = this;
            this.callApi(
                this.$store.getters.prefix + "/static/api.php", {
                    cmd: "status_read",
                    id: this.bookID,
                    state: element.isRead
                },
                "",
                function (rd) {
                    if (!rd) element.isRead = !element.isRead;
                }
            );
        },
        toPlanButtonClick(item) {
            this.bMenu = false;
            let element = this.bListItems[
                this.bListItems.map(el => el.id).indexOf(this.selectedItem.id)
            ];
            element.isToPlan = !element.isToPlan;
            const self = this;
            this.callApi(
                this.$store.getters.prefix + "/static/api.php", {
                    cmd: "status_toplan",
                    id: this.bookID,
                    state: element.isToPlan
                },
                "",
                function (rd) {
                    if (!rd) element.isToPlan = !element.isToPlan;
                }
            );
        },
        favoritesButtonClick(item) {
            this.bMenu = false;
            let element = this.bListItems[
                this.bListItems.map(el => el.id).indexOf(this.selectedItem.id)
            ];
            element.isFavorites = !element.isFavorites;
            const self = this;
            this.callApi(
                this.$store.getters.prefix + "/static/api.php", {
                    cmd: "status_favorites",
                    id: this.bookID,
                    state: element.isFavorites
                },
                "",
                function (rd) {
                    if (!rd) element.isFavorites = !element.isFavorites;
                }
            );
        },
        setStars(n) {
            this.bMenu = false;
            let element = this.bListItems[
                this.bListItems.map(el => el.id).indexOf(this.selectedItem.id)
            ];
            element.howManyStars = n;
            const self = this;
            this.callApi(
                this.$store.getters.prefix + "/static/api.php", {
                    cmd: "set_stars",
                    id: this.bookID,
                    state: element.howManyStars
                },
                "",
                function (rd) {
                    
                }
            );
        },
        starsButton0Click(item) {
            this.setStars(0);
        },
        starsButton1Click(item) {
            this.setStars(1);
        },
        starsButton2Click(item) {
            this.setStars(2);
        },
        starsButton3Click(item) {
            this.setStars(3);
        },
        starsButton4Click(item) {
            this.setStars(4);
        },
        starsButton5Click(item) {
            this.setStars(5);
        },

        onScroll() {
            this.bMenu = false;
        }
    },
    mounted: function () {
        window.addEventListener("resize", this.handleResize);
    },
    beforeDestroy: function () {
        window.removeEventListener("resize", this.handleResize);
    }
};
</script>
