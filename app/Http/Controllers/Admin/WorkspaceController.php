<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class WorkspaceController extends Controller
{
    public function updateOwner(Request $request)
    {
        $email = $request->input('email');
        $workspaceId = $request->input('workspaceId');
        $workspace = Workspace::findOrFail($workspaceId);
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $alreadyOwner = Workspace::where('id', $workspaceId)->where('owner_id', $user->id)->first();
                if ($alreadyOwner) {
                    return 'already owner';
                } else {
                    if ($workspace) {
                        $workspace->owner()->associate($user);
                        $workspace->members()->attach($user);
                        $workspace->save();

                        return $user->firstname . ' is now onwer of ' . $workspace->name;
                    }
                }
            } else {
                return 'User does not exist';
            }
        }
    }

    public function addMember(Request $request)
    {
        $emails = $request->input('email');
        $workspaceId = $request->input('workspaceId');
        $workspace = Workspace::findOrFail($workspaceId);
        $removed = '';
        $alreadyInAWorkspace = false;
        $response = '';
        $emails = ($emails) ? explode(',', $emails) : false;
        
        if (!$workspace) {
            return 'Workspace does not exist';
        }

        if ($emails && is_array($emails)) {
            foreach ($emails as $email) {
                if ($email) {
                    $user = User::where('email', $email)->first();
                    if ($user) {
                        /*$alreadyInAWorkspace = DB::table('workspace_members')->where('user_id', $user->id);
                        if ($alreadyInAWorkspace->first()) {
                            $currentWorkspace = Workspace::findOrFail($alreadyInAWorkspace->first()->workspace_id);
                            if ($currentWorkspace) {
                                $removed .= ' ' . $user->firstname . ' and has been removed from ' . $currentWorkspace->name . ' workspace.';
                            }
                            $alreadyInAWorkspace->delete();
                        }*/
                            if (!$workspace->members()->where('users.id', $user->id)->exists()) {
        
                                $workspace->members()->attach($user);
                                $workspace->save();
            
                                $response .= ' ' . $user->firstname . ' is now part of ' . $workspace->name . $removed;
                            } else {
                                $response .= ' ' . $user->firstname . ' is already in ' . $workspace->name;
                            }
                    } else {
                        $response .= ' ' . 'User does not exist for email' . $email;
                    }
                }
            }
        }

        return $response;
    }

    public function switch(Request $request)
    {
        $workspaceId = $request->input('workspace_id');
        $workspace = Workspace::findOrFail($workspaceId);
        $user = \Auth::user();
        $success = false;
        $code = 403;

        try {
            if ($workspace && $user) {
                $user->current_workspace_id = $workspaceId;
                $user->save();
                $success = true;
                $code = 200;
            }
        } catch (Exception $e) {
            _helper('api')->error($e->getMessage());
        }

        return json_encode(['success' => $success, 'code' => $code]);
    }
}
