<script>

    import IndexComponent from '~~nova~~/views/Index.vue'

    export default {
        extends: IndexComponent,
        created() {
            Nova.$on('megaFilterEncodedQuery', encodedQuery => this.megaFilterQuery = encodedQuery)
            Nova.$on('reloadIndexResources', this.getResources)
            Nova.$on('useMegaFilter', state => this.usesMegaFilter = state)
        },
        data() {
            return {
                megaFilterQuery: false,
                usesMegaFilter: false
            }
        },
        computed: {
            resourceRequestQueryString() {

                return {
                    search: this.currentSearch,
                    filters: this.encodedFilters,
                    orderBy: this.currentOrderBy,
                    orderByDirection: this.currentOrderByDirection,
                    perPage: this.currentPerPage,
                    trashed: this.currentTrashed,
                    page: this.currentPage,
                    viaResource: this.viaResource,
                    viaResourceId: this.viaResourceId,
                    viaRelationship: this.viaRelationship,
                    viaResourceRelationship: this.viaResourceRelationship,
                    relationshipType: this.relationshipType,
                    megaFilter: this.megaFilterQuery
                }

            }
        },
        methods: {
            getResources() {

                setTimeout(() => {

                    if (this.usesMegaFilter === false || this.megaFilterQuery !== false) {

                        IndexComponent.methods.getResources.call(this)

                    }

                })

            }
        }
    }

</script>
