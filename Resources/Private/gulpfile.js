var gulp           = require('gulp');
var autoprefixer   = require('gulp-autoprefixer');
var sass           = require('gulp-sass');
var uglify         = require('gulp-uglify');
var pump           = require('pump');

var sassConfig = {
    outputStyle: 'compressed',
    indentWidth: 4
};

var autoprefixerConfig = {
    browsers: [
        'last 3 versions',
        'ie >= 9'
    ]
};

var uglifyConfig = {
    preserveComments: 'license'
};

/**
 * sassify the scss files and autoprefix them
 */
gulp.task('build:css', function() {
    return gulp.src('Sass/*.scss')
        .pipe(sass(sassConfig)
            .on('error', sass.logError))
        .pipe(autoprefixer(autoprefixerConfig))
        .pipe(gulp.dest('../Public/Css/'));
});

gulp.task('build:js', function(cb) {
    pump([
        gulp.src('Js/*.js'),
        uglify(uglifyConfig),
        gulp.dest('../Public/Js/')
    ], cb);
});

gulp.task('build', ['build:css','build:js']);

/**
 * task which watches files and executes
 * sass-dev and concat-css tasks
 */
gulp.task('watch', ['build'], function() {
    // watch sass
    gulp.watch(['Sass/**/*.scss'], ['build:css']).on('change', function(event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });

    // watch js
    gulp.watch(['Js/**/*.js'], ['build:js']).on('change', function(event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
});

/**
 * default task
 */
gulp.task('default', ['build']);
