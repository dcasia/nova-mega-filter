import MegaFilterCard from './components/MegaFilterCard.vue'
import { registerMixin } from './components/MegaFilter'

Nova.booting(app => {

    const componentFn = app.component

    app.component = function (name, component) {

        if (name === 'FilterMenu') {
            registerMixin(component)
        }

        return componentFn.call(this, name, component)

    }

    app.component('mega-filter-card', MegaFilterCard)

})
