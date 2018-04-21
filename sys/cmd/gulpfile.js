
var gulp = require ('gulp'),
    livereload = require('gulp-livereload'),
    chokidar   = require ('chokidar'),
    apidoc = require('gulp-apidoc');

gulp.task ('api', function () {
  livereload.listen ();

  var watcherReload = chokidar.watch (['./root/app/controller/api/*.+(php)'], {
    ignored: /(^|[\/\\])\../,
    persistent: true
  });

  watcherReload.on ('change', run_apidoc).on ('add', run_apidoc).on ('unlink', run_apidoc);
});

function run_apidoc (path) {
  apidoc ({
    src: './root/app/controller/api/',
    dest: './root/doc/',
    config: './root/',
    includeFilters: [ ".*\\.php$" ],
    template: 'OA-apiDoc-Template'
  },function () {
    gulp.run ('reload');
  });
}

gulp.task ('reload', function () {
  livereload.changed ();
  console.info ('\nReload Browser!\n');
});