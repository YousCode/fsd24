<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Collection;

use App\Models\Format;

/**
 * Class FormatHelper
 * @package App\Http\Helpers
 */
class FormatHelper
{
    /**
     * Retrieve all formats
     *
     * @return Format[]|Collection
     */
    public function all($term = null)
    {
        if ($term) {
            return Format::where('name', 'LIKE', "%$term%")->orderBy('name', 'ASC')->get();
        } else {
            return Format::orderBy('name', 'ASC')->get();
        }
    }

}
