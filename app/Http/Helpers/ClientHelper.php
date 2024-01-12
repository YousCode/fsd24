<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Collection;

use App\Models\Client;

/**
 * Class ClientHelper
 * @package App\Http\Helpers
 */
class ClientHelper
{
    /**
     * Retrieve all clients
     *
     * @return Client[]|Collection
     */
    public function all($term = null)
    {
        if ($term) {
            return Client::where('name', 'like', '%' . $term . '%')->orderBy('name', 'ASC')->get();
        } else {
            return Client::orderBy('name', 'ASC')->get();
        }
    }

}
