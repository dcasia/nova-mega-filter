<template>

    <Card class="nova-mega-filter rounded p-1 overflow-hidden transition"
          :style="{ '--columns-desktop': columns || 2 }"
          :class="{ '--active': filtersAreApplied, '--expanded': collapsed }">

        <div :class="{ 'h-8': collapsed, 'h-14': !collapsed }"
             class="w-full transition-all flex items-center cursor-pointer">

            <div class="toolbar-button pr-2 md:pr-3 flex flex-1 justify-between filter__header">

                <button
                    v-if="!filtersAreApplied"
                    class="pb-1 pt-2 w-full text-xs uppercase tracking-wide text-center font-bold focus:outline-none relative flex justify-end items-center"
                    @click="collapsed = !collapsed">

                    <span>
                        {{ label ?? __('Filters') }}
                    </span>

                    <Icon type="chevron-down" width="14" class="ml-1 transition-all"
                          :class="{ 'rotate-180': collapsed }"/>

                </button>

                <div v-if="filtersAreApplied " class="w-full">

                    <button
                        class="py-2 w-full block text-xs uppercase tracking-wide text-center font-bold focus:outline-none"
                        @click="clearFilters">

                        {{ __('Reset Filters') }}

                    </button>

                </div>

            </div>

        </div>

        <Collapse :when="collapsed">

            <div class="filter__inner bg-gray-900 rounded p-4">

                <div v-if="filters.length">

                    <div class="flex flex-wrap">

                        <div v-for="filter in filters" :key="filter.name" class="filter__loop">

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

            <Collapse :when="!filtersAreApplied">

                <div class="flex justify-center items-center cursor-pointer"
                     @click="collapsed = !collapsed">

                    <Icon type="chevron-up" height="12" class="translate-y-[2px]"/>

                </div>

            </Collapse>

        </Collapse>

    </Card>

</template>

<script>

    import { Collapse } from 'vue-collapsed'

    export default {
        name: 'MegaFilter',
        components: { Collapse },
        emits: [
            'filter-changed',
            'clear-selected-filters',
        ],
        props: {
            filtersAreApplied: Boolean,
            filters: Array,
            columns: Number,
            label: String,
            resourceName: String,
            lens: { type: String, default: '' },
        },
        data() {
            return {
                collapsed: false,
            }
        },
        methods: {
            clearFilters() {
                this.$emit('clear-selected-filters')
            },
            onChange() {
                this.$emit('filter-changed')
            },
        },
        beforeMount() {
            this.collapsed = this.filtersAreApplied
        },
    }

</script>

<style lang="scss" scoped>

    .dark .nova-mega-filter {

        &.\--expanded {
            background-color: rgba(var(--colors-gray-700));
        }

        &.\--active {

            background-color: rgba(var(--colors-primary-500));

            .filter__header {
                color: rgba(var(--colors-gray-800));
            }

        }

        .filter__inner {
            background-color: rgba(var(--colors-gray-900));
        }

        .filter__header {
            color: rgba(var(--colors-gray-400));
        }

        .filter__loop {
            &:hover {
                @apply border-gray-800;
            }
        }

    }

    .nova-mega-filter {

        &.\--expanded {
            background-color: rgba(var(--colors-gray-300));
        }

        &.\--active {

            background-color: rgba(var(--colors-primary-500));

            .filter__header {
                color: white;
            }

        }

        .filter__inner {
            background-color: white;
        }

        .filter__header {
            color: rgba(var(--colors-gray-500));
        }

        --columns-mobile: 1;
        --columns-desktop: 2;

        .filter__loop {

            width: calc(100% / var(--columns-mobile));
            margin: 1px;

            @apply border border-transparent rounded transition-all;

            &:hover {
                @apply border-gray-200;
            }

        }

        @screen lg {

            .filter__loop {
                width: calc(100% / var(--columns-desktop) - 2px);
            }

        }

    }

</style>
