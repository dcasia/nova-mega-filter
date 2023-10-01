const mix = require('laravel-mix')

require('./nova.mix')
require('mix-tailwindcss')

mix
  .setPublicPath('dist')
  .js('resources/js/card.js', 'js')
  .vue({ version: 3 })
  .postCss('resources/css/card.css', 'css')
  .tailwind()
  .nova('digital-creative/nova-mega-filter')
