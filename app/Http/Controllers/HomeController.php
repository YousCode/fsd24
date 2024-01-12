<?php
 
namespace App\Http\Controllers;
 
use app\Http\Controllers\Controller;

 
class HomeController extends Controller
{
    /**
     * Show the profile for a given user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('home');
    }
}