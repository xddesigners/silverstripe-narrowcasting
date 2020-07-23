const mix = require('laravel-mix');

mix
  .setResourceRoot('/app/')
  .js('client/src/js/narrowcasting.js', 'client/dist/js')
  .sass('client/src/styles/narrowcasting.scss', 'client/dist/styles')
  .sass('client/src/styles/narrowcastingcms.scss', 'client/dist/styles');
