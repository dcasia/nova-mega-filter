import { default as MegaFilter } from './components/MegaFilter.vue'
import ProxyIndex from './components/ProxyIndex.vue'

Nova.booting((Vue, router, store) => {

    router.beforeEach((from, to, next) => {

        if (from.name === 'index') {

            return next({ ...from, name: 'index-mega-filter', replace: true })

        }

        next()

    })

    router.addRoutes([
        {
            name: 'index-mega-filter',
            path: '/resources/:resourceName',
            component: ProxyIndex,
            props: true
        }
    ])

    Vue.component('mega-filter', MegaFilter)

})
