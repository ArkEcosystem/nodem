const mix = require('laravel-mix');
const path = require('path');
const focusVisible = require('postcss-focus-visible');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
        resolve: {
            alias: {
                '@ui': path.resolve(__dirname, 'vendor/arkecosystem/foundation/resources/assets/')
            }
        }
    })
    .options({
        processCssUrls: false,
    })
    // App
    .js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss')(),
        focusVisible()
    ])
    .copy('node_modules/swiper/swiper-bundle.min.js', 'public/js/swiper.js')
    .copy('vendor/arkecosystem/foundation/resources/assets/js/file-download.js', 'public/js/file-download.js')
    .copy('vendor/arkecosystem/foundation/resources/assets/js/clipboard.js', 'public/js/clipboard.js')
    .copyDirectory('resources/images', 'public/images')
    // For FiraMono font
    .copyDirectory('resources/fonts', 'public/fonts')
    // Extract node_modules
    .extract(['alpinejs']);

if (mix.inProduction()) {
    mix.version();
}
