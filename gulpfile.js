'use strict';

var gulp = require('gulp'),
    shell = require('gulp-shell'),
    connect = require('gulp-connect-php'),
    del = require('del');

gulp.task('phpspec', shell.task('phpspec run'));

gulp.task('phpunit', ['prepare-test'], shell.task('phpunit --no-coverage'));
gulp.task('codecept', ['prepare-test'], shell.task('codecept run --no-exit --silent --html'));

gulp.task('prepare-test', ['clean-test'], shell.task([
    'vendor/bin/phinx migrate -e test',
    'vendor/bin/phinx seed:run -e test'
]));

gulp.task('server-coverage', function () {
    connect.server({
        port: 8000,
        base: 'public',
        router: 'apicoverage.php'
    });
});
gulp.task('server', function () {
    connect.server({
        port: 8001,
        base: 'public',
        router: 'api.php'
    });
});

gulp.task('apitest-clean', function () {
    return del([
        './apitest/_out',
        './apitest/_coverage',
        './apitest/ApiTest.php'
    ]);
});

gulp.task('apitest-build', ['apitest-clean'], shell.task([
    'mkdir apitest/_coverage',
    'mkdir apitest/_out',
    'php bin/build-apitest.php'
]));
gulp.task('apitest', ['prepare-test', 'apitest-build'], shell.task('vendor/bin/phpunit apitest/ApiTest.php --colors'));

gulp.task('initdb', shell.task('vendor/bin/phinx migrate'));

gulp.task('docs-rest', shell.task('node_modules/.bin/raml2html raml/api.raml > build/api.html'));
gulp.task('docs-code', ['clean-docs'], shell.task('apigen generate --template-theme bootstrap --debug'));

gulp.task('clean-test', function () {
    return del(['./db/data/test.db']);
});
gulp.task('clean-docs', function () {
    return del(['./build/docs']);
});

gulp.task('clean', function () {
    return del(['./build/*']);
});
