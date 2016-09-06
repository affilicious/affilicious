var gulp = require('gulp'),
    sourcemaps = require('gulp-sourcemaps'),
    autoprefixer = require('gulp-autoprefixer'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    typescript = require('gulp-typescript'),
    cleancss = require('gulp-clean-css'),
    compass = require('gulp-compass'),
    imagemin = require('gulp-imagemin'),
    watch = require('gulp-watch');

// Transpile sass
gulp.task('styles', function () {
    var input = './assets/scss/*.scss',
        output = './assets/css';

    return gulp
        .src(input)
        .pipe(compass({
            config_file: './config.rb',
            css: './assets/css',
            sass: './assets/scss',
            image: './assets/images',
            javascripts_dir: '.assets/js',
        }))
        .pipe(autoprefixer(
            'last 2 version',
            'safari 5',
            'ie 7',
            'ie 8',
            'ie 9',
            'opera 12.1',
            'ios 6',
            'android 4'
        ))
        .pipe(gulp.dest(output))
        .pipe(cleancss())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(output));
});

// Transpile typescript
gulp.task('scripts', function () {
    var input = './assets/ts/*.ts',
        output = './assets/js';

    gulp.src(input)
        .pipe(sourcemaps.init())
        .pipe(typescript({
            noImplicitAny: false
        }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(output));

    return gulp.src(input)
        .pipe(typescript({
            noImplicitAny: false
        }))
        .pipe(uglify())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(output));
});

// Automatically transpile sass or typescript
gulp.task('watch', function() {
    var dir = './assets/(scss|ts)/**/*';

    return watch(dir, function() {
        gulp.start(['styles', 'scripts']);
    });
});

// Compress the image size
gulp.task('images', function () {
    var input = './assets/images/**/*',
        output = './assets/images';

    return gulp.src(input)
        .pipe(imagemin())
        .pipe(gulp.dest(output))
});

