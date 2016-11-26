var gulp = require('gulp'),
    sourcemaps = require('gulp-sourcemaps'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    uglifycss = require('gulp-uglifycss'),
    rename = require('gulp-rename'),
    es6 = require('gulp-babel'),
    imageMin = require('gulp-imagemin'),
    order = require('gulp-order'),
    sass = require('gulp-sass'),
    merge = require('merge-stream'),
    watch = require('gulp-watch');

var assetsPath = 'assets/',
    publicPath = assetsPath + 'public/',
    adminPath = assetsPath + 'admin/'
;

var assetPaths = {
    public: {
        css: [],
        sass: [
            'assets/public/scss/**'
        ],
        js: [],
        es6: [
            'assets/public/es6/**'
        ],
        fonts: [],
        images: []
    },

    admin: {
        css: [],
        sass: [
            'assets/admin/scss/**'
        ],
        js: [],
        es6: [
            'assets/admin/es6/**'
        ],
        fonts: [],
        images: []
    }
};

gulp.task('public-css', function() {
    var cssStream = gulp.src(assetPaths.public.css)
        .pipe(concat('css-files.css'))
    ;

    var sassStream = gulp.src(assetPaths.public.sass)
        .pipe(sass())
        .pipe(concat('sass-files.scss'))
    ;

    return merge(cssStream, sassStream)
        .pipe(order(['css-files.css', 'sass-files.scss']))
        .pipe(concat('style.css'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(publicPath + 'css/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(uglifycss())
        .pipe(gulp.dest(publicPath + 'css/'))
    ;
});

gulp.task('admin-css', function() {
    var cssStream = gulp.src(assetPaths.admin.css)
        .pipe(concat('css-files.css'))
    ;

    var sassStream = gulp.src(assetPaths.admin.sass)
        .pipe(sass())
        .pipe(concat('sass-files.scss'))
    ;

    return merge(cssStream, sassStream)
        .pipe(order(['css-files.css', 'sass-files.scss']))
        .pipe(concat('admin.css'))
        .pipe(gulp.dest(adminPath + 'css/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(uglifycss())
        .pipe(gulp.dest(adminPath + 'css/'))
    ;
});

gulp.task('public-js', function () {
    var jsStream = gulp.src(assetPaths.public.js)
        .pipe(concat('js-files.js'))
    ;

    var es6Stream = gulp.src(assetPaths.public.es6)
        .pipe(es6())
        .pipe(concat('es6-files.js'))
    ;

    return merge(jsStream, es6Stream)
        .pipe(order(['js-files.js', 'es6-files.js']))
        .pipe(concat('script.js'))
        .pipe(gulp.dest(publicPath + 'js/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(uglify())
        .pipe(gulp.dest(publicPath + 'js/'))
    ;
});

gulp.task('admin-js', function () {
    var jsStream = gulp.src(assetPaths.admin.js)
        .pipe(concat('js-files.js'))
    ;

    var es6Stream = gulp.src(assetPaths.admin.es6)
        .pipe(es6())
        .pipe(concat('es6-files.js'))
    ;

    return merge(jsStream, es6Stream)
        .pipe(order(['js-files.js', 'es6-files.js']))
        .pipe(concat('admin.js'))
        .pipe(gulp.dest(adminPath + 'js/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(uglify())
        .pipe(gulp.dest(adminPath + 'js/'))
    ;
});

gulp.task('public-images', function() {
    return gulp.src(assetPaths.public.images)
        .pipe(imageMin())
        .pipe(gulp.dest(publicPath + 'images/'))
    ;
});

gulp.task('admin-images', function() {
    return gulp.src(assetPaths.admin.images)
        .pipe(imageMin())
        .pipe(gulp.dest(adminPath + 'images/'))
    ;
});

gulp.task('public-fonts', function() {
    return gulp.src(assetPaths.public.fonts)
        .pipe(gulp.dest(publicPath + 'fonts/'))
    ;
});

gulp.task('admin-fonts', function() {
    return gulp.src(assetPaths.admin.fonts)
        .pipe(gulp.dest(adminPath + 'fonts/'))
    ;
});

gulp.task('public-watch', function() {
    gulp.watch(assetPaths.public.css, ['public-css']);
    gulp.watch(assetPaths.public.sass, ['public-css']);
    gulp.watch(assetPaths.public.js, ['public-js']);
    gulp.watch(assetPaths.public.es6, ['public-js']);
    gulp.watch(assetPaths.public.images, ['public-images']);
    gulp.watch(assetPaths.public.fonts, ['public-fonts']);
});

gulp.task('admin-watch', function() {
    gulp.watch(assetPaths.admin.css, ['admin-css']);
    gulp.watch(assetPaths.admin.sass, ['admin-css']);
    gulp.watch(assetPaths.admin.js, ['admin-js']);
    gulp.watch(assetPaths.admin.es6, ['admin-js']);
    gulp.watch(assetPaths.admin.images, ['admin-images']);
    gulp.watch(assetPaths.admin.fonts, ['admin-fonts']);
});

gulp.task('default', ['public-css', 'public-js', 'public-images', 'public-fonts', 'admin-css', 'admin-js', 'admin-images', 'admin-fonts']);
gulp.task('watch', ['default', 'public-watch', 'admin-watch']);
