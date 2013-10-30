<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	/**
     * Display a main map.
     *
     * @return Response
     */
    public function index()
    {
        $networks = Network::orderBy('created_at', 'desc')->get();

        return View::make('home.index', compact('networks'));
    }

	public function showWelcome()
	{
		return View::make('hello');
	}

}