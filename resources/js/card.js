import MegaFilter from './components/MegaFilter.vue'
import MegaFilterPlaceholder from './components/MegaFilterPlaceholder.vue'

Nova.booting((Vue, router, store) => {

    Vue.component('mega-filter', MegaFilter)
    Vue.component('mega-filter-placeholder', MegaFilterPlaceholder)

})
