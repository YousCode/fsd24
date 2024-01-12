<?php

namespace App\Http\Helpers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

use App\Models\Skill;

/**
 * Class SkillHelper
 * @package App\Http\Helpers
 */
class SkillHelper
{
    /**
     * Retrieve all skills
     *
     * @return Skills[]|Collection
     */
    public function all($term = null)
    {
    		if($term){
    			return Skill::where('name', 'like', '%'.$term.'%')->orderBy('name', 'ASC')->get();
    		} else {
                $skills = Skill::orderBy('name', 'ASC')->get();
                foreach ($skills as $key => $skill) {
                    $skills[$key]->name = __('skills.' . $skill->name);
                }

        	    return $skills;
      	}
    }

    /**
     * [add description]
     * @param [type] $name [description]
     */
    public function add($name)
    {
    	$skill = new Skill;
    	$skill->name = $name;
    	$skill->save();

    	return $skill;
    }

    /**
     * [delete description]
     * @param [type] $id [description]
     */
    public function delete($id)
    {
    	$skill = Skill::find($id);

    	if($skill){
    		$skill->delete();
    	}

    	return 'Skill deleted';
    }

}
