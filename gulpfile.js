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

var sourcePaths = {
    public: {
        css: [],
        sass: [],
        js: [],
        es6: [],
        fonts: [],
        images: []
    },

    admin: {
        css: [],
        sass: [
            'assets/admin/scss/**'
        ],
        js: [],
        es6: [],
        fonts: [],
        images: []
    }
};

gulp.task('public-css', function() {
    var cssStream = gulp.src(sourcePaths.public.css)
        .pipe(concat('css-files.css'))
    ;

    var sassStream = gulp.src(sourcePaths.public.sass)
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
    var cssStream = gulp.src(sourcePaths.admin.css)
        .pipe(concat('css-files.css'))
    ;

    var sassStream = gulp.src(sourcePaths.admin.sass)
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
    var jsStream = gulp.src(sourcePaths.public.js)
        .pipe(concat('js-files.js'))
    ;

    var es6Stream = gulp.src(sourcePaths.public.es6)
        .pipe(es6())
        .pipe(concat('es6-files.js'))
    ;

    return merge(jsStream, es6Stream)
        .pipe(order(['js-files.js', 'es6-files.js']))
        .pipe(concat('script.css'))
        .pipe(gulp.dest(publicPath + 'js/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(uglify())
        .pipe(gulp.dest(publicPath + 'js/'))
    ;
});

gulp.task('admin-js', function () {
    var jsStream = gulp.src(sourcePaths.admin.js)
        .pipe(concat('js-files.js'))
    ;

    var es6Stream = gulp.src(sourcePaths.admin.es6)
        .pipe(es6())
        .pipe(concat('es6-files.js'))
    ;

    return merge(jsStream, es6Stream)
        .pipe(order(['js-files.js', 'es6-files.js']))
        .pipe(concat('admin.css'))
        .pipe(gulp.dest(publicPath + 'js/'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(uglify())
        .pipe(gulp.dest(publicPath + 'js/'))
    ;
});

gulp.task('public-images', function() {
    return gulp.src(sourcePaths.public.images)
        .pipe(imageMin())
        .pipe(gulp.dest(publicPath + 'images/'))
    ;
});

gulp.task('admin-images', function() {
    return gulp.src(sourcePaths.admin.images)
        .pipe(imageMin())
        .pipe(gulp.dest(adminPath + 'images/'))
    ;
});

gulp.task('public-fonts', function() {
    return gulp.src(sourcePaths.public.fonts)
        .pipe(gulp.dest(publicPath + 'fonts/'))
    ;
});

gulp.task('admin-fonts', function() {
    return gulp.src(sourcePaths.admin.fonts)
        .pipe(gulp.dest(adminPath + 'fonts/'))
    ;
});

gulp.task('public-watch', function() {
    gulp.watch(sourcePaths.public.css, ['public-css']);
    gulp.watch(sourcePaths.public.sass, ['public-css']);
    gulp.watch(sourcePaths.public.js, ['public-js']);
    gulp.watch(sourcePaths.public.es, ['public-js']);
    gulp.watch(sourcePaths.public.images, ['public-images']);
    gulp.watch(sourcePaths.public.fonts, ['public-fonts']);
});

gulp.task('admin-watch', function() {
    gulp.watch(sourcePaths.admin.css, ['admin-css']);
    gulp.watch(sourcePaths.admin.sass, ['admin-css']);
    gulp.watch(sourcePaths.admin.js, ['admin-js']);
    gulp.watch(sourcePaths.admin.es, ['admin-js']);
    gulp.watch(sourcePaths.admin.images, ['admin-images']);
    gulp.watch(sourcePaths.admin.fonts, ['admin-fonts']);
});

gulp.task('default', ['public-css', 'public-js', 'public-images', 'public-fonts', 'admin-css', 'admin-js', 'admin-images', 'admin-fonts']);
gulp.task('watch', ['default', 'public-watch', 'admin-watch']);
