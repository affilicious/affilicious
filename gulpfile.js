let gulp = require('gulp'),
    sourcemaps = require('gulp-sourcemaps'),
    uglify = require('gulp-uglify'),
    uglifycss = require('gulp-uglifycss'),
    rename = require('gulp-rename'),
    imageMin = require('gulp-imagemin'),
    sass = require('gulp-sass'),
    watch = require('gulp-watch'),
    concat = require('gulp-concat'),
    babelify = require('babelify'),
    browserify = require("browserify"),
    buffer = require('vinyl-buffer'),
    source = require("vinyl-source-stream"),
    plumber = require('gulp-plumber'),
    autoprefixer = require('gulp-autoprefixer'),
    livereload = require('gulp-livereload'),
    fs = require('fs')
;

let modules = {
    admin: [
        'common',
        'products',
        'carbon-fields',
        'amazon-import'
    ]
};

gulp.task('admin-css', function() {
    modules.admin.map(function(module) {
        gulp.src(`assets/admin/src/${module}/scss/**`)
            .pipe(plumber())
            .pipe(sass())
            .pipe(concat(`${module}.css`))
            .pipe(autoprefixer())
            .pipe(gulp.dest('assets/admin/dist/css/'))
            .pipe(uglifycss())
            .pipe(rename({suffix: '.min'}))
            .pipe(gulp.dest('assets/admin/dist/css/'))
            .pipe(livereload())
        ;
    });
});

gulp.task('admin-js', function () {
     modules.admin.map(function(module) {
        if(fs.existsSync(`assets/admin/src/${module}/js/${module}.js`)) {
            browserify({entries: [`assets/admin/src/${module}/js/${module}.js`], debug: true})
                .transform('babelify', {presets: ['es2015']})
                .bundle()
                .pipe(plumber())
                .pipe(source(`${module}.js`))
                .pipe(buffer())
                .pipe(gulp.dest('assets/admin/dist/js/'))
                .pipe(uglify())
                .pipe(rename({suffix: '.min'}))
                .pipe(gulp.dest('assets/admin/dist/js/'))
                .pipe(livereload())
            ;
        }
    });
});

gulp.task('admin-img', function() {
    modules.admin.map(function(module) {
        gulp.src(`assets/admin/src/${module}/img/**`)
            .pipe(plumber())
            .pipe(imageMin())
            .pipe(gulp.dest('assets/admin/dist/img/'))
        ;
    });
});

gulp.task('admin-watch', function() {
    livereload.listen();

    modules.admin.map(function(module) {
        gulp.watch(`assets/admin/src/${module}/scss/**`, ['admin-css']);
        gulp.watch(`assets/admin/src/${module}/js/**`, ['admin-js']);
        gulp.watch(`assets/admin/src/${module}/img/**`, ['admin-img']);
    });
});

gulp.task('default', ['admin-css', 'admin-js', 'admin-img']);
gulp.task('watch', ['default', 'admin-watch']);
