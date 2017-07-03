let gulp = require('gulp'),
    sourcemaps = require('gulp-sourcemaps'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    uglifycss = require('gulp-uglifycss'),
    rename = require('gulp-rename'),
    imageMin = require('gulp-imagemin'),
    order = require('gulp-order'),
    sass = require('gulp-sass'),
    merge = require('merge-stream'),
    es = require('event-stream'),
    watch = require('gulp-watch'),
    babelify = require('babelify'),
    browserify = require("browserify"),
    buffer = require('vinyl-buffer'),
    source = require("vinyl-source-stream"),
    plumber = require('gulp-plumber'),
    autoprefixer = require('gulp-autoprefixer')
;

let paths = {
    admin: {
        'common': {
            css: [
                'assets/admin/src/common/scss/**',
            ],
            img: [
                'assets/admin/src/common/img/**',
            ]
        },
        'products': {
            css: [
                'assets/admin/src/products/scss/**',
            ],
            js: [
                'assets/admin/src/products/js/products.js',
            ],
        },
        'carbon-fields': {
            css: [
                'assets/admin/src/carbon-fields/scss/**',
            ],
            js: [
                'assets/admin/src/carbon-fields/js/carbon-fields.js',
            ]
        },
        'amazon-import': {
            css: [
                'assets/admin/src/carbon-fields/scss/**',
            ],
            js: [
                'assets/admin/src/amazon-import/js/amazon-import.js',
            ]
        }
    }
};

gulp.task('admin-css', function() {
    Object.keys(paths.admin).map(function(key) {
        let entries = paths.admin[key].css;
        if(entries === undefined) {
            return;
        }

        gulp.src(entries)
            .pipe(plumber())
            .pipe(sass())
            .pipe(concat(`${key}.css`))
            .pipe(autoprefixer())
            .pipe(gulp.dest('assets/admin/dist/css/'))
            .pipe(rename({suffix: '.min'}))
            .pipe(uglifycss())
            .pipe(gulp.dest('assets/admin/dist/css/'))
        ;
    });
});

gulp.task('admin-js', function () {
    Object.keys(paths.admin).map(function(key) {
        let entries = paths.admin[key].js;
        if(entries === undefined) {
            return;
        }

        let streams = entries.map(function(entry) {
            return browserify({entries: [entry], debug: true})
                .transform('babelify', {presets: ['es2015']})
                .bundle()
            ;
        });

        es.merge.apply(null, streams)
            .pipe(plumber())
            .pipe(source(`${key}.js`))
            .pipe(buffer())
            .pipe(gulp.dest('assets/admin/dist/js/'))
            .pipe(rename({suffix: '.min'}))
            .pipe(uglify())
            .pipe(gulp.dest('assets/admin/dist/js/'))
        ;
    });
});

gulp.task('admin-img', function() {
    Object.keys(paths.admin).map(function(key) {
        let entries = paths.admin[key].img;
        if(entries === undefined) {
            return;
        }

        gulp.src(entries)
            .pipe(plumber())
            .pipe(imageMin())
            .pipe(gulp.dest('assets/admin/dist/img/'))
        ;
    });
});

gulp.task('admin-watch', function() {
    Object.keys(paths.admin).map(function(key) {
        let css = paths.admin[key].css;
        if(css !== undefined) {
            gulp.watch(css, ['admin-css']);
        }

        let js = paths.admin[key].js;
        if(js !== undefined) {
            gulp.watch(js, ['admin-js']);
        }

        let img = paths.admin[key].img;
        if(img !== undefined) {
            gulp.watch(img, ['admin-img']);
        }
    });
});

gulp.task('default', ['admin-css', 'admin-js', 'admin-img']);
gulp.task('watch', ['default', 'admin-watch']);
