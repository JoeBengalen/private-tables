'use strict';

var gulp = require('gulp'),
    shell = require('gulp-shell'),
    connect = require('gulp-connect-php'),
    del = require('del');

gulp.task('install', shell.task([
    'composer install',
    'npm install',
    'vendor/bin/phinx migrate'
]));

gulp.task('server', function () {
    connect.server({
        port: 8001,
        base: 'public',
        router: 'api.php'
    });
});

gulp.task('server-coverage', function () {
    connect.server({
        port: 8000,
        base: 'public',
        router: 'apicoverage.php'
    });
});

gulp.task('apitest-clean', function () {
    return del(['./apitest/*']);
});

gulp.task('apitest-reset', shell.task([
    'rm -f db/data/test.db',
    'vendor/bin/phinx migrate -e test',
    'vendor/bin/phinx seed:run -e test'
]));

gulp.task('apitest-build', ['apitest-clean'], shell.task([
    'mkdir apitest/tmp',
    'mkdir apitest/logging',
    'php bin/build-apitest.php'
]));

gulp.task('apitest-run', ['apitest-reset'], shell.task(
    'vendor/bin/phpunit apitest/ApiTest.php --colors'
));

gulp.task('apitest', ['apitest-build', 'apitest-run']);


gulp.task('docs-clean', function () {
    return del([
        'build/docs',
        'build/api.html'
    ]);
});

gulp.task('docs-rest', ['docs-clean'], shell.task(
    'node_modules/.bin/raml2html raml/api.raml > build/api.html'
));
gulp.task('docs-code', ['clean-docs'], shell.task(
    'apigen generate --template-theme bootstrap --debug'
));
