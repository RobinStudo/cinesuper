var Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build/')
    .addEntry('app', './assets/js/app.js')
    .addEntry('register', './assets/js/components/register.js')
    .addEntry('dashboard', './assets/js/components/dashboard.js')
    .addEntry('resetPassword', './assets/js/components/resetPassword.js')
    .addEntry('renewPassword', './assets/js/components/renewPassword.js')
    .addEntry('login', './assets/js/components/login.js')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
