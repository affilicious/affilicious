let gulp = require('gulp'),
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
    public: [
        'universal-box'
    ],
    admin: [
        'common',
        'products',
        'carbon-fields',
        'import',
        'amazon-import'
    ],
    github: [
        'browserstack'
    ]
};

gulp.task('public-css', function() {
    modules.public.map(function(module) {
        gulp.src(`assets/public/src/${module}/scss/**`)
            .pipe(plumber())
            .pipe(sass())
            .pipe(concat(`${module}.css`))
            .pipe(autoprefixer())
            .pipe(gulp.dest('assets/public/dist/css/'))
            .pipe(uglifycss())
            .pipe(rename({suffix: '.min'}))
            .pipe(gulp.dest('assets/public/dist/css/'))
            .pipe(livereload())
        ;
    });
});

gulp.task('public-js', function () {
    modules.public.map(function(module) {
        if(fs.existsSync(`assets/public/src/${module}/js/${module}.js`)) {
            browserify({entries: [`assets/public/src/${module}/js/${module}.js`], debug: true})
                .transform('babelify', {presets: ['es2015']})
                .bundle()
                .pipe(plumber())
                .pipe(source(`${module}.js`))
                .pipe(buffer())
                .pipe(gulp.dest('assets/public/dist/js/'))
                .pipe(uglify())
                .pipe(rename({suffix: '.min'}))
                .pipe(gulp.dest('assets/public/dist/js/'))
                .pipe(livereload())
            ;
        }
    });
});

gulp.task('public-img', function() {
    modules.public.map(function(module) {
        gulp.src(`assets/public/src/${module}/img/**`)
            .pipe(plumber())
            .pipe(imageMin())
            .pipe(gulp.dest('assets/public/dist/img/'))
        ;
    });
});

gulp.task('public-svg', function() {
    modules.public.map(function(module) {
        gulp.src(`assets/public/src/${module}/svg/**`)
            .pipe(plumber())
            .pipe(gulp.dest('assets/public/dist/svg/'))
        ;
    });
});

gulp.task('public-watch', function() {
    livereload.listen();

    modules.public.map(function(module) {
        gulp.watch(`assets/public/src/${module}/scss/**`, ['public-css']);
        gulp.watch(`assets/public/src/${module}/js/**`, ['public-js']);
        gulp.watch(`assets/public/src/${module}/img/**`, ['public-img']);
        gulp.watch(`assets/public/src/${module}/svg/**`, ['public-svg']);
    });
});

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

gulp.task('admin-svg', function() {
    modules.admin.map(function(module) {
        gulp.src(`assets/admin/src/${module}/svg/**`)
            .pipe(plumber())
            .pipe(gulp.dest('assets/admin/dist/svg/'))
        ;
    });
});

gulp.task('admin-watch', function() {
    livereload.listen();

    modules.admin.map(function(module) {
        gulp.watch(`assets/admin/src/${module}/scss/**`, ['admin-css']);
        gulp.watch(`assets/admin/src/${module}/js/**`, ['admin-js']);
        gulp.watch(`assets/admin/src/${module}/img/**`, ['admin-img']);
        gulp.watch(`assets/admin/src/${module}/svg/**`, ['admin-svg']);
    });
});

gulp.task('github-img', function() {
    modules.github.map(function(module) {
        gulp.src(`assets/github/src/${module}/img/**`)
            .pipe(plumber())
            .pipe(imageMin())
            .pipe(gulp.dest('assets/github/dist/img/'))
        ;
    });
});

gulp.task('github-svg', function() {
    modules.github.map(function(module) {
        gulp.src(`assets/github/src/${module}/svg/**`)
            .pipe(plumber())
            .pipe(gulp.dest('assets/github/dist/svg/'))
        ;
    });
});

gulp.task('github-watch', function() {
    livereload.listen();

    modules.github.map(function(module) {
        gulp.watch(`assets/github/src/${module}/img/**`, ['github-img']);
        gulp.watch(`assets/github/src/${module}/svg/**`, ['github-svg']);
    });
});

gulp.task('default', ['public-css', 'public-js', 'public-img', 'public-svg', 'admin-css', 'admin-js', 'admin-img', 'admin-svg', 'github-img', 'github-svg']);
gulp.task('watch', ['default', 'public-watch', 'admin-watch', 'github-watch']);
