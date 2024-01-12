<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Enum\ExplorerRegistrationStatusEnum;

use App\Models\Contact;
use App\Models\ExplorerCode;
use App\Models\ExplorerRegistration;
use App\Models\ProjectFile;
use App\User;
use App\Models\Workspace;

class AdminController extends Controller
{
	public function index()
    {
        $approve = Contact::whereNull('approved_at')->orderBy('id', 'DESC')->get();
        $certified = User::where('certified','=',0)->get();
        $explorerRegistration = ExplorerRegistration::where('status', ExplorerRegistrationStatusEnum::WAITING)
            ->orWhere('status', ExplorerRegistrationStatusEnum::ACCEPTED)
            ->orWhere('status',ExplorerRegistrationStatusEnum::REFUSED)
            ->leftJoin('users', 'user_id', '=', 'users.id')
            ->get();
        $explorerCodes = ExplorerCode::with('users')->get();
        $workspaces = Workspace::with('owner', 'members')->get();
        return view("approve", compact('approve', 'explorerRegistration', 'certified', 'workspaces', 'explorerCodes'));
    }

 	/**
   * [generateFile description]
   * @param  [type] $id     [description]
   * @param  string $action [description]
   * @return [type]         [description]
   */
  public function generateFile($id, $action = 'view')
  {
  	$file = ProjectFile::where('uniqid', $id)->first();

  	if (!empty($file) && file_exists($file->path)){

  		if($action == 'view'){
  			return response()->file($file->path);
  		}

  		if($action == 'download'){
      	return response()->download($file->path);
    	}
  	}

  	response()->json('Accès non autorisé')->send();
  }

}
