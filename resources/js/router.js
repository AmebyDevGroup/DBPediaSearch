import Vue from 'vue';
import VueRouter from 'vue-router';
import DBpediaSearch from "./components/DBpediaSearch";

Vue.use(VueRouter);

export default new VueRouter({
    routes: [
        {path: '/', component: DBpediaSearch}
    ],
    mode: 'history'
})
