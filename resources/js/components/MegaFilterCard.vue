<template>

    <MegaFilter
        class="nova-mega-filter"
        :lens="lens"
        :filters="filters"
        :columns="card.columns"
        :resource-name="resourceName"
        :filters-are-applied="filtersAreApplied"
        @filter-changed="filterChanged"
        @clear-selected-filters="clearSelectedFilters(lens || null)"
    />

</template>

<script>
import Filterable from '@/mixins/Filterable'
import InteractsWithQueryString from '@/mixins/InteractsWithQueryString'
import MegaFilter from './MegaFilter.vue'
import { activeFilterCount, filtered, megaFilterOnly } from './MegaFilter'

export default {
    name: 'MegaFilterCard',
    components: { MegaFilter },
    mixins: [ Filterable, InteractsWithQueryString ],
    props: [
        'card',
        'lens',
        'resourceName',
    ],
    computed: {
        filters() {
            return filtered(this.$store, this.resourceName, megaFilterOnly)
        },
        filtersAreApplied() {
            return activeFilterCount(this.$store, this.resourceName, megaFilterOnly)
        },
        initialEncodedFilters() {
            return this.queryStringParams[ this.filterParameter ] || ''
        },
    },
    created() {
        this.initializeState(this.lens || null)
    }
}

</script>

<style>

.nova-mega-filter {
    min-height: auto;
    padding-top: 0;
}

</style>
