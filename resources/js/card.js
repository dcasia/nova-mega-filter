import { default as MegaFilter, observable } from './components/MegaFilter.vue'
import MegaFilterPlaceholder from './components/MegaFilterPlaceholder.vue'

Nova.booting((Vue, router, store) => {
    Vue.component('mega-filter', MegaFilter)
    Vue.component('mega-filter-placeholder', MegaFilterPlaceholder)
})

Nova.request().interceptors.request.use(request => {

    if (request.params &&
        request.params.hasOwnProperty('filters') &&
        request.params.hasOwnProperty('search')) {

        return new Promise(resolve => {

            const timer = setInterval(() => {

                if (observable.ready === false) {

                    return

                }

                if (request.method === 'get' &&
                    request.url === `/nova-api/${ observable.resourceName }`) {

                    request.params.mega_filter = observable.params

                    clearInterval(timer)

                    return resolve(request)

                }

                return resolve(request)

            })

        })

    }

    return request

})
