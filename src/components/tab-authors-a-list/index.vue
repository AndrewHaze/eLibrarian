<template>
<div class="tb">
    <div class="tb-header">
        <div class="tb-header-left" @click="sortText">
            <div class="tb-header-title">Авторы</div>
            <div class="tb-header-sort-arrows">
                <span class="tb-header-sort-desc" :class="{ active: aDesc }">&#8593;</span>
                <span class="tb-header-sort-asc" :class="{ active: aAsc }">&#8595;</span>
            </div>
        </div>
        <div class="tb-header-right" @click="sortNumeric">
            <div class="tb-header-title">Книг</div>
            <div class="tb-header-sort-arrows">
                <span class="tb-header-sort-desc" :class="{ active: bDesc }">&#8593;</span>
                <span class="tb-header-sort-asc" :class="{ active: bAsc }">&#8595;</span>
            </div>
        </div>
    </div>
    <div class="tb-body">
        <div class="tb-body-element" v-for="item in aItems" :id="item.id" :key="item.id" :ref="item.id" v-if="aFilter == '*' || aFilter == item.author.charAt(0).toUpperCase()" :class="{active: item.isActive}" @click="itemClickHandler">
            <div class="tb-body-element-left">{{ item.author }}</div>
            <div class="tb-body-element-right">{{ item.books }}</div>
        </div>
    </div>
</div>
</template>

<style lang="scss">
%flex {
    display: flex;
    flex: row, nowrap;
    justify-content: space-between;
}

.tb {
    display: flex;
    flex-flow: column nowrap;
    flex: 1 1 auto;
    height: 100%;
    user-select: none;

    .tb-header {
        @extend %flex;
        border: 1px solid #dee2e6;
        padding: 0.8rem 0.3rem 0.8rem 0.65rem;

        .tb-header-left,
        .tb-header-right {
            cursor: pointer;
            @extend %flex;
        }

        .tb-header-left {
            width: 140px;
        }

        .tb-header-title {
            font-weight: bold;
        }

        .tb-header-sort-arrows {
            position: relative;
            width: 1.5rem;

            .tb-header-sort-desc,
            .tb-header-sort-asc {
                position: absolute;
                margin: 0;
                padding: 0;
                opacity: 0.4;
                top: 0;
            }

            .tb-header-sort-desc {
                right: 8px;
            }

            .tb-header-sort-asc {
                right: 0px;
            }

            .active {
                opacity: 1;
            }
        }
    }

    .tb-body {
        overflow: auto;
        height: calc(100% - 50px);

        .tb-body-element {
            @extend %flex;
            border: 1px solid #dee2e6;
            border-bottom: none;
            padding: 0.8rem 0.3rem 0.8rem 0.65rem;
            cursor: pointer;

            &:hover {
                background-color: #eee;
            }

            &:last-child {
                border-bottom: 1px solid #dee2e6;
            }

            .tb-body-element-right {
                display: flex;
                min-width: 4rem;
                justify-content: center;
                align-items: center;
            }
        }

        .active {
            background-color: #ddd;

            &:hover {
                background-color: #ddd;
            }
        }
    }
}
</style>

<script>
import store from "../../store";

function sortTasc(a, b) {
    let x = a.author.toLowerCase();
    let y = b.author.toLowerCase();
    return x < y ? -1 : x > y ? 1 : 0;
}

function sortTdesc(a, b) {
    let x = a.author.toLowerCase();
    let y = b.author.toLowerCase();
    return x > y ? -1 : x < y ? 1 : 0;
}

function sortNasc(a, b) {
    let x = a.books;
    let y = b.books;
    return x < y ? -1 : x > y ? 1 : 0;
}

function sortNdesc(a, b) {
    let x = a.books;
    let y = b.books;
    return x > y ? -1 : x < y ? 1 : 0;
}

export default {
    name: "items-list",
    props: ["aItems", "aFilter"],
    data: function () {
        return {
            aAsc: true,
            aDesc: false,
            bAsc: false,
            bDesc: false
        };
    },
    methods: {
        itemClickHandler(item) {
            //сбросим атрибуты по всему массиву
            this.aItems.forEach(function (entry) {
                entry.isActive = false;
            });
            let element = this.aItems[
                this.aItems.map(el => el.id).indexOf(item.currentTarget.id)
            ];
            store.commit("setAuthorID", element.id.substr(2));
            element.isActive = true;
        },
        sortText() {
            this.bAsc = false;
            this.bDesc = false;
            if (this.aAsc) {
                this.aDesc = true;
                this.aAsc = false;
            } else if (this.aDesc) {
                this.aDesc = false;
                this.aAsc = true;
            } else {
                this.aDesc = false;
                this.aAsc = true;
            }
            if (this.aAsc) this.aItems.sort(sortTasc);
            else this.aItems.sort(sortTdesc);
        },
        sortNumeric() {
            this.aAsc = false;
            this.aDesc = false;
            if (this.bAsc) {
                this.bDesc = true;
                this.bAsc = false;
            } else if (this.bDesc) {
                this.bDesc = false;
                this.bAsc = true;
            } else {
                this.bDesc = false;
                this.bAsc = true;
            }
            if (this.bAsc) this.aItems.sort(sortNasc);
            else this.aItems.sort(sortNdesc);
        }
    },

    updated() {
        if (store.getters.authorID === -1) {
            this.aItems.forEach(function (entry) {
                entry.isActive = false;
            });
        }
    }
};
</script>
