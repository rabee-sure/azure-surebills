const mix = require('laravel-mix');

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

  // *** New Ui Rabee ***//
  mix.js('resources/new/js/app.js', 'public/new/js').vue()
  .sass('resources/new/scss/main.scss', 'public/new/css')
  .sass('resources/new/scss/auth.scss', 'public/new/css')
  .sass('resources/new/scss/print.scss', 'public/new/css')
  .scripts([
    "resources/new/js/bootstrap/bootstrap.min.js",
    "resources/new/js/bopper.min.js",
    "resources/new/js/auth_script.js",
  ],'public/new/js/auth_main.js').sourceMaps()
  .scripts([
    "resources/new/js/popper.min.js",
    "resources/new/js/bootstrap/bootstrap.bundle.min.js",
    "resources/new/js/script.js",
  ],'public/new/js/main.js').sourceMaps()
  // *** New Ui Rabee ***//





 mix.js('resources/js/app.js', 'public/js').vue()

    // .scripts([
    //     "public/js/jquery-3.3.1.min.js",
    //     "public/js/bootstrap.bundle.min.js"
    // ],'public/js/jbootstrap.js').sourceMaps()
    // .scripts([
    //     "public/js/Chart.bundle.min.js",
    //     "public/js/chartjs-plugin-datalabels.js",
    //     "public/js/moment.min.js",
    //     "public/js/fullcalendar.min.js",
    //     "public/js/datatables.min.js",
    //     "public/js/perfect-scrollbar.min.js",
    //     "public/js/glide.min.js",
    //     "public/js/progressbar.min.js",
    //     "public/js/jquery.barrating.min.js",
    //     "public/js/nouislider.min.js",
    //     "public/js/bootstrap-datepicker.js",
    //     // "public/js/Sortable.js",
    //     // "public/js/mousetrap.min.js",
    //     // "public/js/dore.script.js",
    //     "public/js/scripts.js",
    // ],'public/js/all.js').sourceMaps()
    // .styles([
    //     "public/fonts/iconsmind-s/css/iconsminds.css",
    //     "public/fonts/simple-line-icons/css/simple-line-icons.css",
    //     "public/css/bootstrap.min.css",
    //     "public/css/bootstrap.rtl.only.min.css",
    //     "public/css/fullcalendar.min.css",
    //     "public/css/dataTables.bootstrap4.min.css",
    //     "public/css/datatables.responsive.bootstrap4.min.css",
    //     "public/css/perfect-scrollbar.css",
    //     "public/css/glide.core.min.css",
    //     "public/css/bootstrap-stars.css",
    //     "public/css/nouislider.min.css",
    //     "public/css/bootstrap-datepicker3.min.css",
    //     "public/css/component-custom-switch.min.css",
    //     "public/css/main.css",
    // ], 'public/css/all.css')

    // theme file
    // .sass('resources/sass/doretheme/dore.dark.green.scss', 'public/css/dore.dark.green.min.css')
    // .sass('resources/sass/doretheme/dore.light.green.scss', 'public/css/dore.light.green.min.css')

    // .scripts([
    //     "public/js/dore.script.js",
    //     "public/js/scripts.js",
    // ],'public/js/auth.js')
    // .styles([
    //     "public/fonts/iconsmind-s/css/iconsminds.css",
    //     "public/fonts/simple-line-icons/css/simple-line-icons.css",
    //     "public/css/bootstrap.min.css",
    //     "public/css/bootstrap.rtl.only.min.css",
    //     "public/css/bootstrap-float-label.min.css",
    //     "public/css/main.css",
    // ], 'public/css/auth.css')
    // .sass('resources/sass/app.scss', 'public/css')

    // bill details
    .sass('resources/sass/bill_details.scss', 'public/css')

    // bill details
    .js('resources/js/store.js', 'public/js')
    .sass('resources/sass/store.scss', 'public/css')

    .copy('vendor/proengsoft/laravel-jsvalidation/resources/views', 'resources/views/vendor/jsvalidation')
    .copy('vendor/proengsoft/laravel-jsvalidation/public', 'public/vendor/jsvalidation')
    .options({
     processCssUrls: false
   });
