// 'use strict';

// var gulp = require('gulp'),
// 	sass = require('gulp-sass'),
// 	maps = require('gulp-sourcemaps'),
// 	concat = require('gulp-concat'),
// 	plumber = require('gulp-plumber'),
// 	notify = require('gulp-notify');

var options = {
	assets: 'static/',
	src: '_dev/'
}

// gulp.task('scss', function() {
// 	return gulp.src( options.src + 'scss/*.scss')
// 		.pipe(plumber({errorHandler: notify.onError("Error: &lt;%= error.message %&gt;")}))
// 		.pipe(maps.init())
// 		.pipe(concat('cp-admin.css'))
// 		.pipe(sass())
// 		.pipe(maps.write('./'))
// 		.pipe(gulp.dest( options.assets + 'css'))
// 		.pipe(notify({
// 			message: 'all done',
// 			title: 'SCSS'
// 		}))
// 		;
// });

// gulp.task('default', ['scss']);

// gulp.task('watch', function() {
// 	gulp.watch( options.src + 'scss/**/*.scss', ['scss']);
// });

const { series, parallel } = require('gulp');
const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require('gulp-sourcemaps');
const notify = require("gulp-notify");
const plumber = require('gulp-plumber');

function clean(cb) {
  // body omitted
  cb();
}

function scss(cb) {
  return gulp.src( options.src + 'scss/*.scss' )
  	// .pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}))
  	.pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest( options.assets + 'css' ))
    .pipe(notify({
		message: 'all done',
		title: 'SCSS'
	}));
}

function js(cb) {
  // body omitted
  cb();
}

exports.default = series(clean, 
	parallel( scss, js )
);