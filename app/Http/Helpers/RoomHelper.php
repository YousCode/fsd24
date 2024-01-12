<?php

namespace App\Http\Helpers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

use App\Models\Room;

/**
 * Class JobHelper
 * @package App\Http\Helpers
 */
class RoomHelper
{
    /**
     * Retrieve all rooms
     *
     * @param null $term
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return Room::orderBy('name', 'ASC')->get();
    }

}
