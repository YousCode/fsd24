<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class ExplorerController extends Controller
{
    //
    public function index(Request $request)
    {
        return view('pages.explorer.tpl_explorer');
    }

    public function profile($id = null)
    {
        $user = \Auth::user();
        if ($id == null) {
            $userToSee = \Auth::user();
        } else {
            $userToSee = User::find($id);
        }

        return view('pages.explorer.tpl_explorer_profile', compact('user', 'userToSee'));
    }

    public function membership()
    {
        if (\Auth::user()->hasRole('talent')) {
            return view('pages.explorer.tpl_explorer_membership_freelance');
        } else {
            return view('pages.explorer.tpl_explorer_membership_client');
        }
    }

    public function messenger(Request $request)
    {
        $user = User::select('users.id','users.avatar','users.firstname','users.lastname','users.name','users.is_explorer','users.is_user')
            ->where('users.id','=',\Auth::user()->id)->first();
        $conversationId = (int)$request->input('conversationId') ?? 1;
        $currencies = Currency::all();
        return view('pages.explorer.tpl_explorer_messenger', ['conversationId' => $conversationId,'user'=>$user, 'currencies' => json_encode($currencies)]);
    }
}
