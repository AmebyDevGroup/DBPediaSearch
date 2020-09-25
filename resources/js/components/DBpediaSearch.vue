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
                <input type="text" class="menu__input" placeholder="Wyszukaj..." v-model="inputValue">
                <img src="/images/lupa.svg" alt="" class="menu__search" @click="search()">
            </div>
        </div>
        <div class="results" v-if="isSearchActive">
            <div class="results__box">
                <span class="results__empty" v-if="data.length === 0">Wyniki wyszukiwania</span>
                <div v-if="data.length !== 0" class="results__content">
                    <div v-for="item in data" class="results__item">
                        <a :href="item.URI" class="results__name" target="_blank">{{ item.surfaceForm }}</a>
                        <div class="results__uri">{{ item.URI }}</div>
                        <span class="results__info">
                            Additional info:
                            <span v-if="item.types.length !== 0" v-for="elem in item.types">
                                {{ elem }}
                            </span>
                            <span v-else>Empty</span>
                        </span>
                    </div>
                </div>
            </div>
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
          }
        },
        methods: {
            search() {
                if (this.inputValue === '') return;
                axios.post('/data/spotlight',{
                    data: {
                        data: `${this.inputValue}`
                    }
                }).then(data => {
                    console.log('data', data);
                    this.data = data.data;
                    if (!this.isSearchActive) this.isSearchActive = true;
                    this.inputValue = '';
                })
            }
        }
    }
</script>
