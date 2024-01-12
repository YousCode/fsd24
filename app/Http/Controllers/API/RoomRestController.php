<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\RoomHelper;

/**
 * Class RoomRestController
 * @package App\Http\Controllers\API
 */
class RoomRestController extends Controller
{
    /**
     * Helpers
     *
     * @var []
     */
    private $helpers;

    /**
     * RoomRestController constructor.
     */
    public function __construct()
    {
        $this->helpers['api'] = new ApiHelper();
        $this->helpers['room'] = new RoomHelper();
    }

    public function all(Request $request)
    {
        $datas = $this->helpers['room']->all();
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

}
