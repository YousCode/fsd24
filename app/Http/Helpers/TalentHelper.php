<?php

namespace App\Http\Helpers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Models\KolabContact;
use App\Http\Helpers\NotificationHelper;
use App\Models\Workspace;

/**
 * Class TalentHelper
 * @package App\Http\Helpers
 */
class TalentHelper
{
    /**
     * Retrieve all talents
     *
     * @param $request
     * @return \Illuminate\Support\Collection
     */
    public function all($request = [])
    {
    	$users = User::role(['talent','admin','client']);
        $currentPage = 1;

        if(isset($request['applyWorkspace']) && $request['applyWorkspace'] && isset($request['user_id'])) {
            $currentUserWorkspace = User::find($request['user_id'])->currentWorkspace;

            $workspaceMembersIds = DB::table('workspace_members')->select('user_id')->where('workspace_id', $currentUserWorkspace);
            $users->whereIn('users.id', $workspaceMembersIds);
        }

        if(isset($request['workspace'])) {
            $users->join('workspaces', function ($join) use($request) {
                $join->on('users.id', '=', 'workspaces.owner_id')
                   ->orWhere('workspaces.id', '=', $request['workspace']);
            });

            $workspaceMembersIds = DB::table('workspace_members')->select('user_id')->where('workspace_id', $request['workspace']);
            $users->whereIn('users.id', $workspaceMembersIds);
        }

        if (!empty($request['page'])) {
            $currentPage = $request['page'];
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('only_talent', $request)) {
            $users = User::role(['talent']);
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_is_explorer', $request)) {
            $users->where('is_explorer', '=', $request['filter_is_explorer']);
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_alpha', $request)) {
            $users->where('firstname', 'LIKE', $request['filter_alpha'] . '%');
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_name', $request)) {
            $users->where('name', 'LIKE', '%' . $request['filter_name'] . '%');
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_jobs', $request)) {
            $users->whereIn('job_id', $request['filter_jobs'])->get();
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_skills', $request)) {
            $users->whereHas('skills', function ($query) use ($request) {
                $query->whereIn('id', $request['filter_skills']);
            });
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('filter_date', $request)) {
        	$users->whereDoesntHave('tasks', function($query) use ($request) {
					  $query->where('start_date', '<', $request['filter_date'])
					  			->where('end_date', '>=', $request['filter_date']);
					});
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('itemsPerPage', $request)) {
            $users->limit($request['itemsPerPage']);
        }

        if (_helper('api')->checkParameterExistAndNotEmpty('page', $request)) {
            $users->skip(($request['page'] - 1) * $request['page']);
        }

        if(_helper('api')->checkParameterExistAndNotEmpty('term', $request)) {
            return User::where('firstname', 'LIKE', '%' . $request['term'] . '%')
            					->orWhere('lastname', 'LIKE', '%' . $request['term'] . '%')
            					->get();
        }

        if(!empty($_GET['exclude'])){
        	$users->where('id', '!=', $_GET['exclude']);
        }
        if (_helper('api')->checkParameterExistAndNotEmpty('no_paginate', $request)) {
            return $users->select('users.*')
                ->orderBy('firstname', 'ASC')
                ->groupBy('users.id')
                ->get();
        }
        Paginator::currentPageResolver(function() use
        ($currentPage){
            return $currentPage;
        });
        if(empty($request['workspace']))
        {
            return $users
                ->select('users.*')
                ->paginate(6);
        }else{
            return $users
                ->select('users.*')
                ->orderBy('firstname', 'ASC')
                ->groupBy('users.id')
                ->get();
        }
    }

    public function getContact($workspaceId = false)
    {
        $user = User::all();
        $contacts = KolabContact::all();
        $workspace = Workspace::findorFail($workspaceId);
        if ($workspace) {
            $user = $workspace->users();
            $contacts = KolabContact::where('workspace_id', $workspaceId)->get();
        }
        $result = $user->concat($contacts);
        return $result;
    }

    /**
     * Retrieve one Talent by its id
     *
     * @param int $id
     * @return User|User[]|Collection|Model|null
     */
    public function get($id)
    {
        return User::find($id);
    }

    /**
     * Create or update a Talent
     *
     * @param [] $params
     * @return \Illuminate\Support\Collection
     */
    public function addOrUpdate($params)
    {
        $this->helpers['notification'] = new NotificationHelper;
        $talent = $params['talent'];

      $mandatoryParameters = ['email', 'firstname', 'name'];

      // Check if all parameters are provided
      try {
          _helper('api')->checkParameters($talent, $mandatoryParameters);
      } catch (Exception $e) {
          // Return the exception message if error
  				_helper('api')->error($e->getMessage());
  		}

    	if (isset($talent['id']) && $talent['id']) {
    		$action = 'UPDATE';
    		$user = KolabContact::find($talent['id']);
    		/* $user->updated_by = (int)$params['user'][0]; */
    	} else {
    		$action = 'ADD';
    		$user = new KolabContact();
    	/* 	$user->password = Hash::make('kolab'); // Tmp password for user creation
    		$user->created_by = (int)$params['user'][0];
    		$user->updated_by = (int)$params['user'][0]; */
    	}
        $user->workspace_id = $talent['workspace_id'] ?? '';

        $user->id = $talent['id'] ?? null;

    	$user->email = $talent['email'] ?? '';
    	$user->firstname = ucFirst($talent['firstname'] ?? false);
    	$user->name = ucFirst($talent['name'] ?? false);
    	$user->phone = $talent['phone'] ?? false;
    	$user->city = ucFirst($talent['city'] ?? false);
    	$user->country = ucFirst($talent['country'] ?? false);
    	$user->job_id = $talent['job_id'][0] ?? $user->job_id;
    	$user->profile_type = $talent['profile_type'];
    	$user->instagram_url = $talent['instagram_url'] ?? null;
    	$user->vimeo_url = $talent['vimeo_url'] ?? null;
    	$user->profile_url = $talent['profile_url'] ?? null;
    	/* $user->price = $talent['price']; */
    	//$user->youtube_url = $talent['youtube_url'];
    	//$user->name = ucFirst($talent['firstname']) .' '.     ucFirst($talent['name']);
        //$user->profile_type = $talent['profile_type'];
        //$user->token_login = $this->UnlockAccess();
        //$user->user_id = $this->findOrCreateClient($talent->user);
      // First save to make the link to skills and role possible
      try {
          $user->save();
      } catch (Exception $e) {
          _helper('api')->error($e->getMessage());
      }

      $user->skills()->detach();
      if (isset($talent['skills_ids']) && is_array($talent['skills_ids'])) {
          $user->skills()->sync($talent['skills_ids']);
      }


      try {
          $user->save();

          $icon = $action == 'ADD' ? '🆕' : '✅';
          $action = $action == 'ADD' ? 'ajouté' : 'modifié';
	        $this->helpers['notification']->save("$icon Le talent ".$user->name." a bien été ".$action."", $params['user'][0], 'TALENT', $user->id);
      } catch (Exception $e) {
          _helper('api')->error($e->getMessage());
      }

      return $user;
    }

    /**
     * Update profile
     *
     * @param [] $params
     * @return \Illuminate\Support\Collection
     */
    public function updateProfile($params)
    {
    	if (isset($params['user_id']) && $params['user_id']) {
    		$action = 'UPDATE';
    		$user = User::find($params['user_id']);
    		$user->firstname = $params['firstname'];
    		$user->lastname = $params['lastname'];
    		$user->name = $user->firstname . ' ' . $user->lastname;

    		if(!empty($params['password'])){
    			$user->password = Hash::make($params['password']);
    		}

    		$user->save();
    	} else {
    		return false;
    	}

    	return $user;
    }

    /**
     * Delete One Talent
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
    $this->helpers['notification'] = new NotificationHelper;

      $user = KolabContact::find($id);
      try {
      	$name = $user->name;
        $user->skills()->detach();
        $user->delete();

          /*
          No need in contact vue
            $user->projects()->detach();
            $user->tasks()->detach();
          */


        $this->helpers['notification']->save("🗑 Le talent ".$name." a bien été supprimé", null, 'TALENT', $id);
      } catch (Exception $e) {
          _helper('api')->error($e->getMessage());
      }

      return true;
    }


    public function search($term = null, $workspace = false)
    {
        $userSearched = [];
        if ($term && $workspace) {
            $workspaceMembersIds = DB::table('workspace_members')->select('user_id')->where('workspace_id', $workspace)->pluck('user_id')->toArray();
            $userGet = User::where('firstname', 'like', '%' . $term . '%')->orWhere('lastname', 'like', '%' . $term . '%')->get();
            if ($userGet && $workspaceMembersIds) {
                foreach ($userGet as $user) {
                    if (in_array($user->id, $workspaceMembersIds)) {
                         $userSearched[] = $user;
                    }
                }

                return $userSearched;
            } else {
                return false;
            }
        } else {
            return User::all();
        }
    }
    public function UnlockAccess(): string
    {
        $hash = Hash::make('kolab');
        return str_replace("/",".","$hash");
    }
}
