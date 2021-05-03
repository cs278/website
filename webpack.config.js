const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('web/assets')
    .setPublicPath('/assets')
    .setManifestKeyPrefix('assets/')
    .addEntry('main', './assets/main.js')
    .addEntry('cv', './assets/cv.js')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(true)
;

module.exports = Encore.getWebpackConfig();
