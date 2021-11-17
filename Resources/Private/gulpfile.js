const gulp           = require('gulp');
const autoprefixer   = require('gulp-autoprefixer');
const sass           = require('gulp-sass');
const uglify         = require('gulp-uglify');
const pump           = require('pump');

const sassConfig = {
    outputStyle: 'expanded',
    indentWidth: 4
};

const autoprefixerConfig = {
    overrideBrowserslist: 'last 3 versions, Firefox ESR, not ie 11, not dead'
};

const uglifyConfig = {
    output: {
        comments: '/^!/'
    }
};

/**
 * sassify the scss files and autoprefix them
 */
var buildSass = function() {
    return gulp.src('Sass/*.scss')
        .pipe(sass(sassConfig)
            .on('error', sass.logError))
        .pipe(autoprefixer(autoprefixerConfig))
        .pipe(gulp.dest('../Public/Css/'));
};

var buildJs = function(cb) {
    pump([
        gulp.src('Js/*.js'),
        uglify(uglifyConfig),
        gulp.dest('../Public/Js/')
    ], cb);
};

// watch files and build again
var doWatch = function() {
    gulp.watch(['Sass/**/*.scss'], buildSass);
    gulp.watch(['Js/**/*.js'], buildJs);
};

/**********************************************
 * "Public tasks" - can be called via
 * aawbase {taskname}
 *********************************************/

/**
 * Build task: Calls all required sub tasks
 */
 gulp.task('build', gulp.series(buildSass, buildJs));

 /**
  * Watch task: Watches the files and executes the required tasks.
  */
 gulp.task('watch', gulp.series('build', doWatch));

 /**
  * Default task: Calls the build task
  */
 gulp.task('default', gulp.series('build'));
