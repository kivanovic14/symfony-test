var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory were compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access te previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableSingleRuntimeChunk()
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets od the project
    .addEntry('js/app', [
        './node_modules/jquery/dist/jquery.slim.js',
        './node_modules/popper.js/dist/popper.min.js',
        './node_modules/bootstrap/dist/js/bootstrap.min.js',
        './node_modules/holderjs/holder.min.js'])
    .addStyleEntry('css/app', [
        './node_modules/bootstrap/dist/css/bootstrap.min.css',
        './assets/css/app.css'
    ])

    // uncomment if you use Sass/SCSS files
    // .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global ariable
    // .autoProvidejQuery()
;
module.exports = Encore.getWebpackConfig();
