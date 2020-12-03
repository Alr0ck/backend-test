<?php

$router->get('/signature', function () use ($router) {
    return response(array('api' => env('APP_NAME').' V1'), 200)
        ->header('Content-Type', 'application/json');
});

$router->group(['prefix' => 'companies'], function () use ($router) {
    Route::get('/', 'Company\CompanyController@index');
    Route::post('/', ['middleware' => 'auth', 'uses' => 'Company\CompanyController@store']);
    Route::get('/{id:[0-9]+}', 'Company\CompanyController@show');
    Route::put('/{id:[0-9]+}', ['middleware' => 'auth', 'uses' => 'Company\CompanyController@update']);
    Route::delete('/{id:[0-9]+}', ['middleware' => 'auth', 'uses' => 'Company\CompanyController@destroy']);
});

$router->group(['prefix' => 'employees'], function () use ($router) {
    Route::get('/', 'Employee\EmployeeController@index');
    Route::post('/', ['middleware' => 'auth', 'uses' => 'Employee\EmployeeController@store']);
    Route::get('/{id:[0-9]+}', 'Employee\EmployeeController@show');
    Route::put('/{id:[0-9]+}', ['middleware' => 'auth', 'uses' => 'Employee\EmployeeController@update']);
    Route::delete('/{id:[0-9]+}', ['middleware' => 'auth', 'uses' => 'Employee\EmployeeController@destroy']);
});

Route::post('/logins', 'Auth\AuthController@doLogin');
Route::post('/registers', 'Auth\AuthController@doRegister');

Route::get('/files/{name}','FileManager\FileController@show');