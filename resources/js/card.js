Nova.booting((Vue, router, store) => {
  Vue.component('mega-filter', require('./components/Card'))
  Vue.component('mega-filter-placeholder', require('./components/MegaFilterPlaceholder'))
})
