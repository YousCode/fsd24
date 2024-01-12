<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->middleware(['auth','verified']);
    } */

 	public function index()
 	{
        if (\Auth::user()) {
            return view('pages.tpl_dashboard');
        }
        return view('home');
 	}

}
