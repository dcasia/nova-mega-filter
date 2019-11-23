const mix = require('laravel-mix')

mix.setPublicPath('dist')
    .js('resources/js/card.js', 'js')
