<template>
    <div class="app">
        <div class="home" v-if="!isSearchActive">
            <img src="/images/dbpedia.svg" alt="" class="home__logo">
            <textarea class="home__textarea" v-model="inputValue"></textarea>
            <button class="home__button" @click="search()">
                <img src="/images/lupa.svg" alt="" class="home__search">
                Szukaj
            </button>
        </div>
        <div class="menu" v-if="isSearchActive">
            <img src="/images/dbpedia.svg" alt="" class="menu__logo" @click="isSearchActive = false">
            <div class="menu__input-wrapper">
                <input type="text" class="menu__input" placeholder="Wyszukaj..." v-model="inputValue" @keyup.enter="search()">
                <img src="/images/lupa.svg" alt="" class="menu__search" @click="search()">
            </div>
        </div>
        <div class="results" v-if="isSearchActive">
            <div class="results__number">Znaleziono {{ data.length }} wyników</div>
            <div class="results__box">
                <span class="results__empty" v-if="data.length === 0">Nie znaleziono wyników</span>
                <div v-if="data.length !== 0" class="results__content">
                    <div v-for="item in data" class="results__item">
                        <div class="results__relative">
                            <a :href="item.URI" class="results__name" target="_blank">{{ item.surfaceForm }}</a>
                            <img src="/images/rdf_ico.svg" alt="" class="results__rdf" @click="getRDFData(item)">
                        </div>
                        <div class="results__uri">{{ item.URI }}</div>
                        <span class="results__info">
                            Additional info:
                            <span v-if="item.types.length !== 0" v-for="elem in item.types">
                                {{ elem }}
                            </span>
                            <span v-if="item.types.length === 0">Empty</span>
                        </span>
                    </div>
                </div>
            </div>
            <div v-html="rdfHtml" v-if="rdfHtml" class="results__slot"></div>
        </div>
        <div class="loader" v-if="isLoading">
            <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
          return {
              inputValue: '',
              isSearchActive: false,
              data: [],
              rdfHtml: false,
              isLoading: false,
              clickedItem: '',
          }
        },
        methods: {
            search() {
                if (this.inputValue === '') return;
                axios.post('/data/spotlight',{
                    data: {
                        data: `${this.inputValue}`
                    }
                }).then(res => {
                    this.data = res.data;
                    if (!this.isSearchActive) this.isSearchActive = true;
                    this.inputValue = '';
                    this.rdfHtml = false;
                })
            },
            getRDFData(item) {
                if (!this.rdfHtml) this.clickedItem = '';
                if (this.clickedItem === item.surfaceForm) {
                    this.rdfHtml = false;
                    return;
                }
                this.clickedItem = item.surfaceForm;
                this.isLoading = true;
                axios.post('/data/rdf',{
                    data: {
                        uri: `${item.URI}`
                    }
                }).then(res => {
                    this.rdfHtml = res.data.data;
                    this.isLoading = false;
                })
            }
        }
    }
</script>
