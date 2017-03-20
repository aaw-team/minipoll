var gulp           = require('gulp');
var autoprefixer   = require('gulp-autoprefixer');
var sass           = require('gulp-sass');

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

/**
 * sassify the scss files and autoprefix them
 */
gulp.task('build', function() {
    return gulp.src('Sass/*.scss')
        .pipe(sass(sassConfig)
            .on('error', sass.logError))
        .pipe(autoprefixer(autoprefixerConfig))
        .pipe(gulp.dest('../Public/Css/'));
});

/**
 * task which watches files and executes
 * sass-dev and concat-css tasks
 */
gulp.task('watch', ['build'], function() {
    // watch sass
    gulp.watch(['Sass/**/*.scss'], ['build']).on('change', function(event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
});

/**
 * default task
 */
gulp.task('default', ['build']);
