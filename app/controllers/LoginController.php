<?php

class LoginController extends BaseController {

	/**
	 * Login form
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('login.index');
	}

	/**
	 * Registration form.
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('login.register');
	}

	/**
	 * Registering new user and storing him to DB.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = User::$rules;

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::back()->withInput()->withErrors($validator);
		}

		$user = new User;
		$user->email = Input::get('email');
		$user->username = Input::get('username');
		$user->password = Hash::make(Input::get('password'));
		$user->save();

		Auth::loginUsingId($user->id);

		return Redirect::home()->with('message', 'Thank you for registration!');
	}

	/**
	 * Log in to site.
	 *
	 * 
	 * @return Response
	 */
	public function login()
	{
        if (Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password')), true)
            || Auth::attempt(array('username' => Input::get('email'), 'password' => Input::get('password')), true))	{
            
            if (!Auth::user()->isRegular()) {
                return Redirect::to('dashboard');
            }
            
            return Redirect::intended('/');
        }

        return Redirect::back()->withInput(Input::except('password'))->with('message', 'Неверные данные для логина!');
	}

	/**
	 * Log out from site.
	 *
	 * @return Response
	 */
	public function logout()
	{
        Auth::logout();

        return Redirect::home()->with('message', 'See you later!');
	}

}
