<template>

    <div class="rounded bg-primary-500 p-1">

        <div class="bg-gray-900 rounded p-4">

            <div v-if="filters.length">

                <div class="flex flex-wrap">

                    <div v-for="filter in filters" :key="filter.name" class="w-1/2">

                        <component
                            :is="filter.component"
                            :filter-key="filter.class"
                            :lens="lens"
                            :resource-name="resourceName"
                            @change="onChange"
                            @input="onChange"
                        />

                    </div>

                </div>

            </div>

        </div>

    </div>

</template>

<script>

    import Filterable from '@/mixins/Filterable'
    import InteractsWithQueryString from '@/mixins/InteractsWithQueryString'
    import { cleanUpInterceptors, interceptors } from './RequestHighjacker'

    export default {
        name: 'MegaFilter',
        mixins: [ Filterable, InteractsWithQueryString ],
        emits: [ 'filter-changed' ],
        props: [
            'filters',
            'realResourceName',
            'resourceName',
            'viaResource',
            'viaResourceId',
            'viaRelationship',
        ],
        methods: {
            onChange() {

                this.filterChanged()

                Nova.$emit('refresh-resources')

            },
        },
        computed: {
            initialEncodedFilters() {
                return this.queryStringParams[ this.filterParameter ] || ''
            },
            pageParameter() {
                return this.viaRelationship
                    ? this.viaRelationship + '_page'
                    : this.realResourceName + '_page'
            },
        },
        async created() {

            interceptors.push(config => {

                if (config.params === undefined || config.params === null) {
                    config.params = {}
                }

                if (config.method === 'get' && config.url === `/nova-api/${ this.realResourceName }`) {
                    config.params.filters = this.encodedFilters
                }

                return config

            })

            await this.initializeState()

        },
        unmounted() {
            cleanUpInterceptors()
        },
    }

</script>

<style lang="scss">

</style>
