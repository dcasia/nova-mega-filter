import reduce from 'lodash/reduce'

export function withoutMegaFilter(filter) {
    return filter.megaFilter !== true
}

export function megaFilterOnly(filter) {
    return filter.megaFilter === true
}

export function filtered(store, resource, callback) {
    return store.getters[ `${resource}/filters` ].filter(callback)
}

export function activeFilterCount(store, resource, callback) {

    const filters = filtered(store, resource, callback)

    return reduce(filters, (result, filter) => {
        const originalFilter = store.getters[ `${resource}/getOriginalFilter` ](filter.class)
        const originalFilterCloneValue = JSON.stringify(
            originalFilter.currentValue
        )
        const currentFilterCloneValue = JSON.stringify(filter.currentValue)
        return currentFilterCloneValue === originalFilterCloneValue
            ? result
            : result + 1
    }, 0)

}

export function resetComputed(component) {

    component.computed.filters = function () {
        return filtered(this.$store, this.resourceName, withoutMegaFilter)
    }

    component.computed.filtersAreApplied = function () {
        return this.activeFilterCount > 0
    }

    component.computed.activeFilterCount = function () {
        return activeFilterCount(this.$store, this.resourceName, withoutMegaFilter)
    }

}
