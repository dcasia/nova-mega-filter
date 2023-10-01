import MegaFilterCard from './components/MegaFilterCard.vue'
import resourceStore from '@/store/resources'

Nova.booting((Vue, store) => {

    store.registerModule('mega-filter-store', resourceStore)

    Vue.component('mega-filter-card', MegaFilterCard)

})
