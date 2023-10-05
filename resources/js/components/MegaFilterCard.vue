<template>

    <MegaFilter
        class="nova-mega-filter"
        :lens="lens"
        :filters="card.filters"
        :columns="card.columns"
        :resource-name="resourceName"
        :via-resource="viaResource"
        :via-resource-id="viaResourceId"
        :via-relationship="viaRelationship"
    />

</template>

<script>

    import MegaFilter from './MegaFilter.vue'

    export default {
        name: 'MegaFilterCard',
        components: { MegaFilter },
        props: [
            'card',
            'lens',
            'resourceName',
            'viaResource',
            'viaResourceId',
            'viaRelationship',
        ],
        created() {

            const standardFilters = this.$store.getters[ `${ this.resourceName }/originalFilters` ]
            const merged = standardFilters.concat(this.card.filters.map(filter => ({ ...filter, megaFilter: true })))

            this.$store.commit(`${ this.resourceName }/storeFilters`, merged)

        },
    }

</script>

<style>

    .nova-mega-filter {
        min-height: auto;
        padding-top: 0;
    }

</style>
