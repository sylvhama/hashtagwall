var gulp = require('gulp');
var useref = require('gulp-useref');
var gulpif = require('gulp-if');
var gutil = require('gulp-util');
var plumber = require('gulp-plumber');
var coffee = require('gulp-coffee');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var imagemin = require('gulp-imagemin');
var minifyCss = require('gulp-minify-css');
var minifyHtml = require('gulp-minify-html');
var compass = require('gulp-compass');
var del = require('del');

//gulp-rev to rename

var paths = {
    coffees: ['app/scripts/**/*.coffee'],
    js: ['app/scripts/**/*.js'],
    styles: ['app/styles/**/*.scss'],
    images: 'app/images/**/*',
};

gulp.task('clean', function(cb) {
    del(['dist'], cb);
});

gulp.task('coffee', function() {
    return gulp.src(paths.coffees)
      .pipe(plumber())
      .pipe(coffee({bare: true})).on('error', gutil.log)
      .pipe(gulp.dest('app/scripts'));
});

gulp.task('styles', function() {
    gulp.src(paths.styles)
      .pipe(plumber())
      .pipe(compass({
          config_file: './config.rb',
          css: 'app/styles',
          sass: 'app/styles'
      }))
      .pipe(gulp.dest('app/styles'));
});

gulp.task('html', function () {
  var assets = useref.assets();

  return gulp.src('app/*.html')
    .pipe(assets)
    .pipe(gulpif('*.js', uglify()))
    .pipe(gulpif('*.css', minifyCss()))
    .pipe(assets.restore())
    .pipe(useref())
    .pipe(gulpif('*.html', minifyHtml({
      empty: true,
      spare: true,
      quotes: true
    })))
    .pipe(gulp.dest('dist'));
});

gulp.task('images', function() {
    return gulp.src(paths.images)
      .pipe(imagemin({optimizationLevel: 5}))
      .pipe(gulp.dest('dist/images'));
});

gulp.task('views', function () {
  return gulp.src('app/views/**/*.html')
    .pipe(minifyHtml({
      empty: true,
      spare: true,
      quotes: true
    }))
    .pipe(gulp.dest('dist/views'));
});

gulp.task('partials', function () {
  return gulp.src('app/partials/**/*.html')
    .pipe(minifyHtml({
      empty: true,
      spare: true,
      quotes: true
    }))
    .pipe(gulp.dest('dist/partials'));
});

gulp.task('php', function () {
  return gulp.src('app/php/**/*.*')
    .pipe(gulp.dest('dist/php'));
});

gulp.task('ie', function () {
  gulp.src('app/scripts/respond.js')
    .pipe(gulp.dest('dist/scripts'));

  return gulp.src('app/styles/ie.css')
    .pipe(minifyCss())
    .pipe(gulp.dest('dist/styles'));
});

gulp.task('my-assets', function () {

  gulp.src('app/data/*.json')
    .pipe(gulp.dest('dist/data'));

  gulp.src('app/favicon.ico')
    .pipe(gulp.dest('dist'));

  gulp.src('app/*.png')
    .pipe(gulp.dest('dist'));

  gulp.src('app/*.txt')
    .pipe(gulp.dest('dist'));

  return gulp.src('app/*.xml')
    .pipe(gulp.dest('dist'));
});


gulp.task('watch', function() {
  gulp.watch(paths.styles, ['styles']);
  gulp.watch(paths.coffees, ['coffee']);
});

gulp.task('build', ['html', 'images', 'views', 'partials', 'php', 'my-assets', 'ie']);
gulp.task('default', ['watch']);