<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| 
| Se seguira la siguiente convencion:
| metodos que crean: store y la ruta se llamara add 
| metodos que editan: edit y la ruta se llamara edit
| metodos que borran: delete y ruta delete
| metodos que listan: get y ruta se llamara index
| 
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('auth')->group(function (){
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('recover', 'AuthController@recover');
    Route::post('edit', 'AuthController@edit');
    Route::get('forgotPassword', 'AuthController@recoveryPassword');        
});

Route::group(['middleware' => ['jwt.auth']], function() {
    
    Route::prefix('auth')->group(function (){
        Route::get('logout', 'AuthController@logout');
        Route::get('me', 'AuthController@me');
    });
    
    Route::prefix('roles')->group(function (){
        Route::get('get', 'RoleController@index');
        Route::post('add', 'RoleController@store');
        Route::post('edit', 'RoleController@edit');
        Route::post('delete', 'RoleController@delete');
        Route::get('getRolesPermissions', 'RoleController@AllRolesPermissions');
        Route::post('editRolesPermissions', 'RoleController@changeRolePermissions');
    });

    Route::prefix('permissions')->group(function (){
        Route::get('user', 'AuthController@userPermission');
        Route::get('get', 'RoleController@indexPermission');
        Route::post('add', 'RoleController@addPermission');
        Route::post('edit', 'RoleController@editPermission');
        Route::post('delete', 'RoleController@deletePermission');
    });

    Route::prefix('products')->group(function (){
        Route::get('get', 'ProductController@index');
        Route::post('add', 'ProductController@store');
        Route::post('edit', 'ProductController@edit');
        Route::post('delete', 'RoleController@delete');
    });

    Route::prefix('user')->group(function (){
        Route::get('get', 'UserController@index');
        Route::get('add', 'UserController@store');
        Route::get('edit', 'UserController@edit');
        Route::get('delete', 'UserController@delete');
    });


});
