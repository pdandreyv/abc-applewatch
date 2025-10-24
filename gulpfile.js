const gulp = require('gulp');
const browserSync = require('browser-sync').create();
const concat = require('gulp-concat');
const terser = require('gulp-terser');
const cleanCSS = require('gulp-clean-css');
const autoprefixer = require('gulp-autoprefixer');
const notify = require('gulp-notify');
const gcmq = require('gulp-group-css-media-queries');
const imagemin = require('gulp-imagemin');
const dartSass = require('sass');
const gulpSass = require('gulp-sass')(dartSass);

const config = {
	paths: {
		//base: 'assets',
		entry: {
			js: [
				'templates/src/vendors/jquery/jquery-3.5.1.min.js',
				'templates/src/vendors/bootstrap-4.5.0-dist/js/bootstrap.min.js',
				'templates/src/vendors/jquery/jquery-validation-1.19.1/jquery.validate.min.js',
				'templates/src/vendors/jquery/jquery-validation-1.19.1/additional-methods.min.js',
				'templates/src/vendors/jquery/jquery-validation-1.19.1/localization/messages_ru.js',
				'templates/src/js/common.js'
			],
			scss: [
				'templates/src/vendors/bootstrap-4.5.0-dist/css/bootstrap.min.css',
				'templates/src/scss/*.scss'
			],
			imgs: 'templates/src/imgs/**/*'
		},
		output: {
			js: 'templates/assets/js',
			css: 'templates/assets/css',
			imgs: 'templates/assets/imgs'
		},
		watch: {
			scss: 'templates/src/scss/*.scss',
			js: 'templates/src/js/**/*.js'
		}
	}
}

/*
gulp.task('browser-sync', function () {
	browserSync({
		proxy: '',
		// baseDir: config.paths.base,
		notify: false,
	})
})
*/

//комплилим жс
gulp.task('js', function () {
	return gulp
		.src(config.paths.entry.js, { allowEmpty: true })
		.pipe(concat('bundle.min.js'))
		.pipe(terser())
		.pipe(gulp.dest(config.paths.output.js))
		.pipe(browserSync.stream())
})

//компилим css
gulp.task('scss', function () {
	return gulp
		.src(config.paths.entry.scss, { allowEmpty: true })
		.pipe(gulpSass({ outputStyle: 'expanded' }).on('error', notify.onError()))
		.pipe(autoprefixer({ overrideBrowserslist: ['last 2 versions'], cascade: false }))
		.pipe(gcmq())
		.pipe(cleanCSS())
		.pipe(concat('bundle.min.css'))
		.pipe(gulp.dest(config.paths.output.css))
		.pipe(browserSync.stream())
})

//сжимаем картинки
gulp.task('imagemin', function () {
	return gulp
		.src(config.paths.entry.imgs)
		.pipe(imagemin([
			imagemin.mozjpeg({quality: 75, progressive: true}),
			imagemin.optipng({optimizationLevel: 5})
		]))
		.pipe(gulp.dest(config.paths.output.imgs))
})

//смотрим за изменением файлов и перекомпиливаем стили и скрипты
gulp.task('watch', function () {
	gulp.watch(config.paths.watch.scss, gulp.series('scss'))
	gulp.watch(config.paths.watch.js, gulp.series('js'))
})

//все полностью собираем
gulp.task('build', gulp.parallel('scss','js'))
