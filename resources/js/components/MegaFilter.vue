<template>

    <card class="flex flex-col p-6" @dblclick.native.self="toggleAllSections">

        <div class="flex justify-between items-center">

            <h4>{{ settings.headerLabel }}</h4>

            <div>

                <Button v-if="hasColumns"
                        @click.native="toggleSection('columns')"
                        :state="sections.columns">

                    {{ settings.columnsLabel }}

                </Button>

                <Button v-if="hasFilters"
                        @click.native="toggleSection('filters')"
                        :state="sections.filters">

                    {{ settings.filtersLabel }}

                </Button>

                <Button v-if="hasActions"
                        @click.native="toggleSection('actions')"
                        :state="sections.actions">

                    {{ settings.actionsLabel }}

                </Button>

            </div>

        </div>

        <CollapseTransition v-if="hasColumns">

            <Section v-if="sections.columns"
                     :title="settings.columnsSectionTitle"
                     :reset-label="settings.columnsResetLinkTitle"
                     @reset="resetColumns">

                <div class="pb-2" v-for="column of columns" :class="[ card.settings.columnsWidth ]">

                    <label :for="column.attribute" class="label flex items-center">

                        <input :id="column.attribute"
                               class="checkbox "
                               type="checkbox"
                               v-model="fieldsModel[column.attribute]"
                               @change="refreshResourceTable">

                        <span class="ml-3">{{ column.label }}</span>

                    </label>

                </div>

            </Section>

        </CollapseTransition>

        <CollapseTransition>

            <CollapseTransition v-if="hasFilters && this.sections.filters">

                <Section v-if="sections.filters"
                         :title="settings.filtersSectionTitle"
                         :reset-label="settings.filtersResetLinkTitle"
                         @reset="clearSelectedFilters">

                    <FadeTransition tag="card" group :duration="100"
                                    class="filters w-full border border-50 bg-40 shadow-none p-4 flex flex-wrap">

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

        <CollapseTransition v-if="hasActions">

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
                            {{ __('Run Action on all matching resources (:count)', { count: selectedResourcesCount }) }}
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
    import { Filterable, InteractsWithQueryString } from 'laravel-nova'
    import HandlesActions from './mixins/HandlesActions'
    import Section from './elements/Section'
    import Button from './elements/Button'

    export default {
        mixins: [ Filterable, InteractsWithQueryString, HandlesActions ],
        components: { Button, FadeTransition, CollapseTransition, Section },
        props: [
            'card',
            // 'resource',
            // 'resourceId',
            'resourceName'
        ],
        data() {

            return {
                selectedActionKey: null,
                message: null,
                fieldsModel: {},
                actionMessages: [],
                sections: {
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

            this.initializeFieldsUsingObject(this.$route.query)

        },
        computed: {
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

                    if (filter.hasOwnProperty('megaFilterFieldAttribute')) {

                        return this.fieldsModel[ filter.megaFilterFieldAttribute ]

                    }

                    return true

                })

            },
            columns() {

                return this.card.columns

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

            },
            queryString() {

                return this.indexComponent.queryString

            }
        },
        methods: {
            initializeFieldsUsingObject(query = {}) {

                const attributes = this.columns.map(column => column.attribute)
                const allValues = attributes.map(attribute => {

                    if (query.hasOwnProperty(attribute)) {

                        return {
                            [ attribute ]: query[ attribute ] === 'true'
                        }

                    }

                    const column = this.card.columns.find(column => column.attribute === attribute)

                    return {
                        [ attribute ]: !!column.checked
                    }

                })

                this.fieldsModel = allValues.reduce((left, right) => ({ ...right, ...left }))

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

            },
            toggleAllSections() {

                const state = this.sections.columns === this.sections.filters === this.sections.actions === true

                this.sections.columns = !state
                this.sections.filters = !state
                this.sections.actions = !state

            },
            updateFilters() {

                const removedFilters = this.card.filters.filter(filter => !this.filters.includes(filter))

                for (const filter of removedFilters) {

                    this.$store.commit(`${ this.resourceName }/updateFilterState`, {
                        filterClass: filter.class, value: null
                    })

                }

                this.filterChanged()

            },
            refreshResourceTable() {

                this.updateQueryString({ ...this.fieldsModel })
                this.updateFilters()

                this.$nextTick(async () => {

                    await this.indexComponent.getResources()

                })

            }
        }
    }
</script>

<style lang="scss">

    .filters h3 {

        background-color: transparent;
        text-transform: capitalize;
        color: var(--black);

    }

</style>
