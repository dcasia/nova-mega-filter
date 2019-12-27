import { default as MegaFilter } from './components/MegaFilter.vue'
import MegaFilterPlaceholder from './components/MegaFilterPlaceholder.vue'
import ProxyIndex from './components/ProxyIndex.vue'

Nova.booting((Vue, router, store) => {

    router.beforeEach((from, to, next) => {

        if (from.name === 'index') {

            return next({ ...from, name: 'index-mega-filter' })

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
    Vue.component('mega-filter-placeholder', MegaFilterPlaceholder)

})
