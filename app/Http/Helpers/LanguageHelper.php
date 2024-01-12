<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Collection;

use App\Models\Language;

/**
 * Class FormatHelper
 * @package App\Http\Helpers
 */
class LanguageHelper
{
    /**
     * Retrieve all languages
     *
     * @return Language[]|Collection
     */
    public function all($term = null)
    {
        if ($term) {
            return Language::where('name', 'LIKE', "%$term%")->orderBy('name', 'ASC')->get();
        } else {
            return Language::orderBy('name', 'ASC')->get();
        }
    }

    /**
     * [add description]
     * @param [type] $name [description]
     */
    public function add($name)
    {
    	$language = new Language;
    	$language->name = $name;
    	$language->save();

    	return $language;
    }

    /**
     * [delete description]
     * @param [type] $id [description]
     */
    public function delete($id)
    {
    	$language = Language::find($id);

    	if($language){
    		$language->delete();
    	}

    	return 'Language deleted';
    }

}
