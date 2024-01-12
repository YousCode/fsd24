<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Collection;

use App\Models\Resolution;

/**
 * Class ResolutionHelper
 * @package App\Http\Helpers
 */
class ResolutionHelper
{
    /**
     * Retrieve all resolutions
     *
     * @return Resolution[]|Collection
     */
    public function all($term = null)
    {
        if ($term) {
            return Resolution::where('name', 'LIKE', "%$term%")->orderBy('name', 'ASC')->get();
        } else {
            return Resolution::orderBy('name', 'ASC')->get();
        }
    }

    /**
     * [add description]
     * @param [type] $name [description]
     */
    public function add($name)
    {
    	$resolution = new Resolution;
    	$resolution->name = $name;
    	$resolution->save();

    	return $resolution;
    }

    /**
     * [delete description]
     * @param [type] $id [description]
     */
    public function delete($id)
    {
    	$resolution = Resolution::find($id);

    	if($resolution){
    		$resolution->delete();
    	}

    	return 'Resolution deleted';
    }

}
