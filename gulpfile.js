'use strict';

var gulp = require('gulp'),
	sass = require('gulp-sass'),
	maps = require('gulp-sourcemaps'),
	concat = require('gulp-concat'),
	plumber = require('gulp-plumber'),
	notify = require('gulp-notify');

var options = {
	assets: 'static/',
	src: '_dev/'
}

gulp.task('scss', function() {
	return gulp.src( options.src + 'scss/*.scss')
		.pipe(plumber({errorHandler: notify.onError("Error: &lt;%= error.message %&gt;")}))
		.pipe(maps.init())
		.pipe(concat('cp-admin.css'))
		.pipe(sass())
		.pipe(maps.write('./'))
		.pipe(gulp.dest( options.assets + 'css'))
		.pipe(notify({
			message: 'all done',
			title: 'SCSS'
		}))
		;
});

gulp.task('default', ['scss']);

gulp.task('watch', function() {
	gulp.watch( options.src + 'scss/**/*.scss', ['scss']);
});