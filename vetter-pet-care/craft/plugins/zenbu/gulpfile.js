var gulp = require('gulp'),
    sass = require('gulp-ruby-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    //cssnano = require('gulp-cssnano'),
    //jshint = require('gulp-jshint'),
    uglify = require('gulp-uglify'),
    //imagemin = require('gulp-imagemin'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    notify = require('gulp-notify'),
    compass = require('gulp-compass'),
    pump = require('pump');

//  ----------------------------------------
//  Package styles.
//  Wait for styles-compass to complete before
//  running the task
//  ----------------------------------------

gulp.task('styles', ['styles-compass'], function() {
  gulp.src(['resources/css/zenbu_main.css', 'resources/css/font-awesome.min.css'])
    .pipe(concat('zenbu.css'))
    .pipe(gulp.dest('resources/css/'))
    .pipe(notify({ message: 'Concatenated Zenbu and Font-Awesome CSS files' }));
});

//  ----------------------------------------
//  Compass
//  Return stream as hint that task is completed.
//  ----------------------------------------

gulp.task('styles-compass', function() {
  var stream = gulp.src('resources/scss/**/*.scss')
    .pipe(compass({
      config_file: 'resources/config.rb',
      css: 'resources/css',
      sass: 'resources/scss'
    }))
    .pipe(notify({ message: 'Compass has run' }));
  return stream; // return the stream as the completion hint
});

//  ----------------------------------------
//  Copy some files
//  ----------------------------------------

gulp.task('copy', function(){
  gulp.src([
      'bower_components/lodash/dist/*.js',
      ])
      .pipe(gulp.dest('resources/js/lodash/'))
      .pipe(notify({ message: 'Copy task complete' }));
  gulp.src([
      'bower_components/vue/dist/*.js',
      ])
      .pipe(gulp.dest('resources/js/vue2/'))
      .pipe(notify({ message: 'Copy task complete' }));
});

//  ----------------------------------------
//  JS concatenation and processing
//  ----------------------------------------

gulp.task('js', function(cb) {
    // Concatenate zenbu.main.js
    gulp.src([
              'resources/js/zenbu/zenbu.main.js',
              'resources/js/jquery-ui.min.js',
              'resources/js/lodash/lodash.min.js',
              'resources/js/vue2/vue.js',
              'resources/js/zenbu/zenbu.main.vue.js'])
      .pipe(concat('zenbu.main.min.js'))
      //.pipe(uglify())
      .pipe(gulp.dest('resources/js/'))
      .pipe(notify({ message: 'Done cleaning up the JS for zenbu.main' }));
    gulp.src([
              'resources/js/zenbu/zenbu.display_settings.js',
              'resources/js/jquery-ui.min.js',
              'resources/js/lodash/lodash.min.js',
              'resources/js/vue2/vue.min.js', 'resources/js/zenbu/zenbu.display_settings.vue.js'])
      .pipe(concat('zenbu.display_settings.min.js'))
      .pipe(uglify())
      .pipe(gulp.dest('resources/js/'))
      .pipe(notify({ message: 'Done cleaning up the JS for zenbu.display_settings' }));
    gulp.src([
              'resources/js/zenbu/zenbu.main.js',
              'resources/js/jquery-ui.min.js',
              'resources/js/lodash/lodash.min.js',
              'resources/js/vue2/vue.min.js',
              'resources/js/zenbu/zenbu.saved_searches.vue.js'])
      .pipe(concat('zenbu.saved_searches.min.js'))
      .pipe(uglify())
      .pipe(gulp.dest('resources/js/'))
      .pipe(notify({ message: 'Done cleaning up the JS for zenbu.saved_searches' }));
});

//  ----------------------------------------
//  JS concatenation and processing - Zenbu Main
//  ----------------------------------------

gulp.task('js_main', function() {
  gulp.src([
              'resources/js/zenbu/zenbu.main.js',
              'resources/js/jquery-ui.min.js',
              'resources/js/lodash/lodash.min.js',
              'resources/js/vue2/vue.min.js',
              'resources/js/zenbu/zenbu.main.vue.js'])
      .pipe(concat('zenbu.main.min.js'))
      .pipe(uglify())
      .pipe(gulp.dest('resources/js/'))
      .pipe(notify({ message: 'Done cleaning up the JS for zenbu.main' }));
});

//  ----------------------------------------
//  JS concatenation and processing - Zenbu Display Settings
//  ----------------------------------------

gulp.task('js_display_settings', function() {
  gulp.src([
              'resources/js/zenbu/zenbu.display_settings.js',
              'resources/js/jquery-ui.min.js',
              'resources/js/lodash/lodash.min.js',
              'resources/js/vue2/vue.min.js',
              'resources/js/zenbu/zenbu.display_settings.vue.js'])
      .pipe(concat('zenbu.display_settings.min.js'))
      .pipe(uglify())
      .pipe(gulp.dest('resources/js/'))
      .pipe(notify({ message: 'Done cleaning up the JS for zenbu.display_settings' }));
});

//  ----------------------------------------
//  JS concatenation and processing - Zenbu Saved Searches
//  ----------------------------------------

gulp.task('js_saved_searches', function() {
  gulp.src([
              'resources/js/zenbu/zenbu.main.js',
              'resources/js/jquery-ui.min.js',
              'resources/js/lodash/lodash.min.js',
              'resources/js/vue2/vue.min.js',
              'resources/js/zenbu/zenbu.saved_searches.vue.js'])
      .pipe(concat('zenbu.saved_searches.min.js'))
      .pipe(uglify())
      .pipe(gulp.dest('resources/js/'))
      .pipe(notify({ message: 'Done cleaning up the JS for zenbu.saved_searches' }));
});

//  ----------------------------------------
//  Watcher
//  ----------------------------------------

gulp.task('watch', function() {

  // Watch .scss files
  gulp.watch('resources/scss/**/*.scss', ['styles']);

  // Watch .js files
  gulp.watch('resources/js/zenbu/*.js', ['js_main']);
  gulp.watch('resources/js/zenbu/*.js', ['js_display_settings']);
  gulp.watch('resources/js/zenbu/*.js', ['js_saved_searches']);

  // Watch image files
  //gulp.watch('src/images/**/*', ['images']);

});