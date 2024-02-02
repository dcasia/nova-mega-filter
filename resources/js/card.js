import MegaFilterCard from './components/MegaFilterCard.vue'
import { resetComputed } from './components/MegaFilter'

Nova.booting(app => {

    const componentFn = app.component

    app.component = function (name, component) {

        if (name === 'ResourceTableToolbar') {
            resetComputed(component)
        }

        return componentFn.call(this, name, component)

    }

    app.component('mega-filter-card', MegaFilterCard)

})
