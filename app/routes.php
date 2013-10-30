<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array('as' => 'home', 'uses' => 'HomeController@index'));

Route::get('logout', array('as' => 'login.logout', 'uses' => 'LoginController@logout'));

Route::group(array('before' => 'un_auth'), function()
{
    Route::get('login', array('as' => 'login.index', 'uses' => 'LoginController@index'));
    Route::get('register', array('as' => 'login.register', 'uses' => 'LoginController@create'));
    Route::post('login', array('uses' => 'LoginController@login'));
    Route::post('register', array('uses' => 'LoginController@store'));
});

//Route::resource('networks', 'NetworksController');
Route::get('debug', array('as' => 'networks.debug', 'uses' => 'NetworksController@debug'));

Route::get('networks', array('as' => 'networks.index', 'uses' => 'NetworksController@index'));

Route::get('bssid/{bssid}', array('as' => 'networks.dssid', 'uses' => 'NetworksController@showBssid'));

Route::get('by_type/{name}', array('as' => 'networks.by_type', 'uses' => 'NetworksController@byType'))->where('name', '[A-Za-z0-9 -_]+');

Route::get('by_capability/{name}', array('as' => 'networks.by_capability', 'uses' => 'NetworksController@byCapability'))->where('name', '[A-Za-z0-9 -_]+');

Route::group(array('before' => 'admin.auth'), function()
{
    Route::get('dashboard', function()
    {
        return View::make('login.dashboard');
    });

	Route::group(array('before' => 'admin_role_only'), function()
    {

    	Route::resource('capabilities', 'CapabilitiesController');

    	Route::resource('types', 'TypesController');

    	Route::resource('locations', 'LocationsController');

    	Route::resource('users', 'UsersController');

    	Route::resource('roles', 'RolesController');

        Route::resource('imports', 'ImportsController');

        Route::get('networks/create', array('as' => 'networks.create', 'uses' => 'NetworksController@create'));

        Route::post('networks', array('as' => 'networks.store', 'uses' => 'NetworksController@store'));

        Route::get('networks/{networks}/edit', array('as' => 'networks.edit', 'uses' => 'NetworksController@edit'));

        Route::put('networks/{networks}', array('as' => 'networks.update', 'uses' => 'NetworksController@update'));

        Route::delete('networks/{networks}', array('as' => 'networks.destroy', 'uses' => 'NetworksController@destroy'));

    });

});

Route::get('networks/{networks}', array('as' => 'networks.show', 'uses' => 'NetworksController@show'));

Route::filter('admin_role_only', function()
{
    if (!Auth::user()->isAdmin()) {
        return Redirect::intended('/')->withMessage('Недостаточно прав!');
    }
});

Route::filter('manager_role_only', function() 
{
    if (!Auth::user()->isManager()) {
        return Redirect::intended('/')->withMessage('Недостаточно прав!');
    }
});

Route::filter('moderator_role_only', function() 
{
    if (!Auth::user()->isModerator()) {
        return Redirect::intended('/')->withMessage('Недостаточно прав!');
    }
});

Route::filter('admin.auth', function() 
{
    if (Auth::guest()) {
        return Redirect::to('login');
    }
});

Route::filter('un_auth', function() 
{
    if (!Auth::guest()) {
        Auth::logout();
    }
});

Route::filter('not_guest', function(){
    if (Auth::guest()) {
        return Redirect::intended('/')->withInput()->with('message', 'Для этих действий требуется логин.');
    }
});

Route::filter('regular_user', function(){ //Похоже этот фильтр работает только если ты не гость и не регулярный, тоесть сработает например на админе, проверить!
    if (!Auth::guest()) {
        if (!Auth::user()->isRegular()) {
            return Redirect::back()->with('message', 'У вас недостаточно прав для этого действия.');
        }
    }
});