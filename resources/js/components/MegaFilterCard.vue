<template>

    <MegaFilter
        class="mega-filter-card"
        :filters="card.filters"
        :realResource-name="resourceName"
        :resource-name="customResourceName"
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
            'resourceName',
            'viaResource',
            'viaResourceId',
            'viaRelationship',
        ],
        created() {
            this.$store.commit(`${ this.customResourceName }/clearFilters`)
            this.$store.commit(`${ this.customResourceName }/storeFilters`, this.card.filters)
        },
        computed: {
            customResourceName() {
                return 'mega-filter-store'
            },
        },
    }

</script>

<style lang="scss">

    .mega-filter-card {
        min-height: auto;
    }

</style>
