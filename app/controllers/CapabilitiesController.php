<?php

class CapabilitiesController extends BaseController {

	/**
	 * Capability Repository
	 *
	 * @var Capability
	 */
	protected $capability;

	public function __construct(Capability $capability)
	{
		$this->capability = $capability;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$capabilities = $this->capability->all();

		return View::make('capabilities.index', compact('capabilities'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('capabilities.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Capability::$rules);

		if ($validation->passes())
		{
			$this->capability->create($input);

			return Redirect::route('capabilities.index');
		}

		return Redirect::route('capabilities.create')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$capability = $this->capability->findOrFail($id);

		return View::make('capabilities.show', compact('capability'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$capability = $this->capability->find($id);

		if (is_null($capability))
		{
			return Redirect::route('capabilities.index');
		}

		return View::make('capabilities.edit', compact('capability'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = array_except(Input::all(), '_method');
		$validation = Validator::make($input, Capability::$rules);

		if ($validation->passes())
		{
			$capability = $this->capability->find($id);
			$capability->update($input);

			return Redirect::route('capabilities.show', $id);
		}

		return Redirect::route('capabilities.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->capability->find($id)->delete();

		return Redirect::route('capabilities.index');
	}

}
