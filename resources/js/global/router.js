import Vue from 'vue'
import Router from 'vue-router'
// import store from './Store'

Vue.use(Router)

const router = new Router ({
    routes: [
        { path: '/', redirect: '/search', },

        // Dashboard
        {
            path: '/search',
            name: 'search',
            component: require ('./../pages/search').default,
        }
    ],
})


router.beforeEach((to, from, next) => {
    //store.commit('showLoader')
    next()
})

router.afterEach((to, from) => {
    /*setTimeout(()=>{
        store.commit('hideLoader')
    },1000)*/
})

export default router
