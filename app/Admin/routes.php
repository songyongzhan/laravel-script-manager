<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('/crontabs/platform', 'CrontabController@platform');
    $router->get('/crontabs/platformInfo', 'CrontabController@platformInfo');
    $router->get('/crontabs/restart', 'CrontabController@restart');
    $router->get('/crontabs/start', 'CrontabController@start');
    $router->get('/crontabs/stop', 'CrontabController@stop');
    $router->resource('crontabs', CrontabController::class);
    $router->resource('crontab-run-logs', CrontabRunLogController::class);
});
