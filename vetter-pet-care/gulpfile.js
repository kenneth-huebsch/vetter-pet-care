var gulp = require('gulp')
var sass = require('gulp-sass')
var postcss = require('gulp-postcss')
var uglify = require('gulp-uglify')
var autoprefixer = require('autoprefixer')

gulp.task('styles', function() {
   gulp.src('public/assets/scss/**/*.scss')
       .pipe(sass({
        outputStyle: 'compressed',
        errLogToConsole: false,
        onError: function(err) {
            return notify().write(err);
        }
    }))
       .pipe(postcss([
          require("postcss-cssnext")([ autoprefixer({ browsers: ['> 5%', 'last 2 versions'] }) ]),
        ]))
       .pipe(gulp.dest('public/assets/css/'))
});

gulp.task('js', function(){
	gulp.src('public/assets/js/**/*.js')
       .pipe(uglify())
       .pipe(gulp.dest('public/assets/js/min'))
})

//Watch task
gulp.task('default', ['styles'], function() {
   gulp.watch('public/assets/scss/**/*.scss',['styles']);
});
