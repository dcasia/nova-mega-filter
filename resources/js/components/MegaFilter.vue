<template>

    <card class="mega-filter flex flex-col p-6" @dblclick.native.self="toggleAllSections">

        <div class="flex justify-between items-center">

            <h4>{{ settings.headerLabel }}</h4>

            <div>

                <Button v-if="hasColumns" @click.native="toggleSection('columns')" :state="sections.columns">

                    {{ settings.columnsLabel }}

                </Button>

                <Button v-if="hasFilters" @click.native="toggleSection('filters')" :state="sections.filters">

                    {{ settings.filtersLabel }}

                </Button>

                <Button v-if="hasActions" @click.native="toggleSection('actions')" :state="sections.actions">

                    {{ settings.actionsLabel }}

                </Button>

            </div>

        </div>

        <CollapseTransition class="mega-filter__columns" v-if="hasColumns">

            <Section v-if="sections.columns"
                     :title="settings.columnsSectionTitle"
                     :reset-label="settings.columnsResetLinkTitle"
                     @reset="resetColumns">

                <div class="pb-2" v-for="column of columns" :class="[ card.settings.columnsWidth ]">

                    <label :for="column.attribute" class="label flex items-center">

                        <input :id="column.attribute"
                               class="checkbox"
                               type="checkbox"
                               :disabled="isResourceTableLoading"
                               v-model="fieldsModel[column.attribute]"
                               @change="refreshResourceTable">

                        <span class="ml-3">{{ column.label }}</span>

                    </label>

                </div>

            </Section>

        </CollapseTransition>

        <CollapseTransition class="mega-filter__filters">

            <CollapseTransition v-if="hasFilters && this.sections.filters">

                <Section v-if="sections.filters"
                         :title="settings.filtersSectionTitle"
                         :reset-label="settings.filtersResetLinkTitle"
                         @reset="clearSelectedFilters">

                    <FadeTransition tag="card" group :duration="100"
                                    class="filters w-full border border-50 bg-40 shadow-none p-4 flex flex-wrap justify-between">

                        <component class="flex flex-col inline-flex"
                                   :class="[ card.settings.filtersWidth ]"
                                   v-for="filter in filters"
                                   :resource-name="resourceName"
                                   :key="filter.name"
                                   :filter-key="filter.class"
                                   :is="filter.originalComponent || filter.component"
                                   @input="filterChanged"
                                   @change="filterChanged"/>

                    </FadeTransition>

                </Section>

            </CollapseTransition>

        </CollapseTransition>

        <CollapseTransition class="mega-filter__actions" v-if="hasActions">

            <Section v-if="sections.actions" :title="settings.actionsSectionTitle">

                <div slot="filters" class="flex items-center">

                    <select v-model="selectedActionKey"
                            class="form-control form-select mr-2"
                            v-if="!isLonelyAction">

                        <option :value="null" disabled selected>{{ __('Select Action') }}</option>

                        <option v-for="action in actions"
                                :value="action.uriKey"
                                :key="action.urikey"
                                :selected="action.uriKey === selectedActionKey">

                            {{ action.name }}

                        </option>

                    </select>

                    <button class="btn btn-default btn-primary cursor-pointer"
                            :disabled="isResourceTableEmpty || isResourceTableLoading || (filters.length > 1 && !selectedActionKey)"
                            @click="determineActionStrategy">

                        <div v-if="isLonelyAction">

                            {{ actions[0].name }}

                        </div>

                        <div v-else>
                            {{ __('Run action on all matching resources (:count)', { count: selectedResourcesCount }) }}
                        </div>

                    </button>

                </div>

                <div v-for="message of actionMessages"
                     class="text-sm font-bold text-success-dark w-full card bg-success-light justify-between shadow-none p-4 flex flex-wrap mt-2">

                    {{ message }}

                </div>

            </Section>

        </CollapseTransition>

        <portal to="modals" transition="fade-transition">

            <component v-if="confirmActionModalOpened"
                       class="text-left"
                       :is="selectedAction.component"
                       :working="working"
                       :selected-resources="selectedResources"
                       :resource-name="resourceName"
                       :action="selectedAction"
                       :errors="errors"
                       @confirm="executeAction"
                       @close="closeConfirmationModal"/>

        </portal>

    </card>

</template>

<script>

    import { CollapseTransition, FadeTransition } from 'vue2-transitions'
    import { Filterable, mapProps } from 'laravel-nova'
    import Section from './elements/Section'
    import Button from './elements/Button'
    import HandlesActions from '~~nova~~/mixins/HandlesActions'
    import { escapeUnicode } from '~~nova~~/util/escapeUnicode'
    import _ from 'lodash'

    export default {
        name: 'MegaFilter',
        mixins: [ Filterable, HandlesActions ],
        components: { Button, FadeTransition, CollapseTransition, Section },
        props: {
            card: { type: Object },
            ...mapProps([
                'resourceName',
                'viaResource',
                'viaResourceId',
                'viaRelationship'
            ])
        },
        data() {

            const sections = this.getSectionsFromCache()

            return {
                selectedActionKey: null,
                message: null,
                fieldsModel: {},
                actionMessages: [],
                filterKey: 'DigitalCreative\\MegaFilter\\MegaFilterColumns',
                sections: sections || {
                    columns: this.card.settings.columnsActive,
                    filters: this.card.settings.filtersActive,
                    actions: this.card.settings.actionsActive
                }
            }

        },
        mounted() {

            if (this.isLonelyAction) {

                this.selectedActionKey = this.actions[ 0 ].uriKey

            }

            const filter = this.$store.getters[ `${ this.resourceName }/getFilter` ](this.filterKey)

            this.initializeFieldsUsingObject(
                filter.currentValue || this.decodeObject(this.resolveEncodedFiltersFromCache())
            )

        },
        computed: {
            /**
             * Get the name of the page query string variable.
             */
            pageParameter() {
                return this.viaRelationship
                    ? this.viaRelationship + '_page'
                    : this.resourceName + '_page'
            },
            hasColumns() {

                return this.columns.length > 0

            },
            hasFilters() {

                return this.filters.length > 0

            },
            hasActions() {

                return this.actions.length > 0

            },
            settings() {

                return this.card.settings

            },
            actions() {

                return this.card.actions

            },
            filters() {

                return this.card.filters.filter(filter => {

                    if (filter.class === this.filterKey) {

                        return false

                    }

                    if (filter.hasOwnProperty('megaFilterFieldAttribute')) {

                        return this.fieldsModel[ filter.megaFilterFieldAttribute ]

                    }

                    return true

                })

            },
            exportedColumns() {

                const attributes = { ...this.permanentColumns, ...this.fieldsModel }
                const attributesWithLabels = {}

                for (const attribute in attributes) {

                    if (attributes[ attribute ]) {

                        attributesWithLabels[ attribute ] = this.columns.find(column => column.attribute === attribute).label

                    }

                }

                return attributesWithLabels

            },
            columns() {

                return this.card.columns.filter(column => !column.permanent)

            },
            permanentColumns() {

                return this.card.columns
                    .filter(column => column.permanent)
                    .map(({ attribute }) => ({ [ attribute ]: true }))
                    .reduce((left, right) => ({ ...right, ...left }), {})

            },
            isLonelyAction() {

                return this.actions.length === 1

            },
            isResourceTableLoading() {

                return this.indexComponent.loading

            },
            isResourceTableEmpty() {

                return this.indexComponent.resources.length === 0

            },
            indexComponent() {

                return this.findIndexComponent()

            },
            selectedResourcesCount() {

                if (Array.isArray(this.selectedResources)) {

                    return this.selectedResources.length

                }

                return 'All'

            },
            /**
             * If no items are selected on the main resource table
             * assume user wants to export all matched resources
             */
            selectedResources() {

                const selectedResources = this.indexComponent.selectedResourcesForActionSelector

                if (Array.isArray(selectedResources) && selectedResources.length === 0) {

                    return 'all'

                }

                return selectedResources

            }
        },
        methods: {
            getCache(property) {

                try {

                    return JSON.parse(localStorage.getItem(this.card.cacheKey))[ property ]

                } catch (error) {

                    return null

                }

            },
            getSectionsFromCache() {

                return this.getCache('sections')

            },
            updateCache() {

                const query = this.getEncodedQueryString()

                localStorage.setItem(this.card.cacheKey, JSON.stringify({ query, sections: this.sections }))

            },
            resolveEncodedFiltersFromCache() {

                return this.getCache('query')

            },
            updateQueryString(value) {

                this.$router.replace({ query: { ...this.$route.query, ...value } })

            },
            actionFormData() {

                const formData = new FormData()

                formData.append('columns', JSON.stringify(this.exportedColumns))
                formData.append('filters', this.$route.query[ this.resourceName + '_filter' ])

                return _.tap(formData, formData => {

                    formData.append('resources', this.selectedResources)

                    _.each(this.selectedAction.fields, field => {
                        field.fill(formData)
                    })

                })
            },
            initializeFieldsUsingObject(data = {}) {

                const attributes = this.columns.map(column => column.attribute)
                const allValues = attributes.map(attribute => {

                    if (data.hasOwnProperty(attribute)) {

                        return {
                            [ attribute ]: data[ attribute ] === true
                        }

                    }

                    const column = this.card.columns.find(column => column.attribute === attribute)

                    return {
                        [ attribute ]: !!column.checked
                    }

                })

                this.fieldsModel = allValues.reduce((left, right) => ({ ...right, ...left }), {})

            },
            resetColumns() {

                this.initializeFieldsUsingObject()
                this.refreshResourceTable()

            },
            findIndexComponent(element = this) {

                if (element.hasOwnProperty('getResources')) {

                    return element

                }

                return this.findIndexComponent(element.$parent)

            },
            toggleSection(section) {

                this.sections[ section ] = !this.sections[ section ]

                this.updateCache()

            },
            toggleAllSections() {

                const state = this.sections.columns === this.sections.filters === this.sections.actions === true

                this.sections.columns = !state
                this.sections.filters = !state
                this.sections.actions = !state

                this.updateCache()

            },
            async clearSelectedFilters(lens) {

                if (lens) {

                    await this.$store.dispatch(`${ this.resourceName }/resetFilterState`, {
                        resourceName: this.resourceName,
                        lens
                    })

                } else {

                    _.each(this.$store.getters[ `${ this.resourceName }/originalFilters` ], filter => {

                        if (filter.class !== this.filterKey) {

                            this.$store.commit(`${ this.resourceName }/updateFilterState`, {
                                filterClass: filter.class,
                                value: filter.currentValue
                            })

                        } else {

                            this.updateColumnsFilter()

                        }

                    })

                }

                this.updateQueryString({
                    [ this.pageParameter ]: 1,
                    [ this.filterParameter ]: ''
                })

            },
            updateFilters() {

                const removedFilters = this.card.filters.filter(filter => !this.filters.includes(filter))

                for (const filter of removedFilters) {

                    this.$store.commit(`${ this.resourceName }/updateFilterState`, {
                        filterClass: filter.class, value: null
                    })

                }

                if (removedFilters.length) {

                    this.filterChanged()

                }

            },
            decodeObject(encodedFilters) {

                try {

                    return JSON.parse(atob(encodedFilters))

                } catch (e) {

                    return {}

                }

            },
            encodeObject(data) {

                return btoa(escapeUnicode(JSON.stringify(data)))

            },
            getEncodedQueryString() {

                return this.encodeObject({ ...this.fieldsModel })

            },
            refreshResourceTable() {

                this.updateColumnsFilter()
                this.filterChanged()
                this.updateCache()

            },
            updateColumnsFilter() {

                this.$store.commit(`${ this.resourceName }/updateFilterState`, {
                    filterClass: this.filterKey,
                    value: this.fieldsModel
                })

            }
        }

    }

</script>

<style lang="scss">

    .mega-filter {

        height: auto

    }

    .filters h3 {

        background-color: transparent;
        text-transform: capitalize;
        color: var(--black);

    }

</style>
