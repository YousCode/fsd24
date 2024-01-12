<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UserHelper;
use App\User;

class FirstLoginController extends Controller
{

    public function index($id = null)
    {
        $user = \Auth::user();
        if ($id == null && !UserHelper::isFreelance($user)) {
            $userToSee = \Auth::user();
        } else {
            $userToSee = User::find($id);
        }
        return view('pages.tpl_register_talents',compact('user', 'userToSee'));
    }

    public function create()
    {

    }

}
