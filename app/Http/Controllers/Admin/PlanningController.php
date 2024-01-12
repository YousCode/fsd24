<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlanningController extends Controller
{
 
 	public function index()
 	{

 		// Get datas

 		return view('pages.tpl_planning');
 	}

}
