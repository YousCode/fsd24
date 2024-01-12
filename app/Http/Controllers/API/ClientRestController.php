<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\ClientHelper;

class ClientRestController extends Controller
{
    //
    /**
     * Helpers
     *
     * @var []
     */
    private $helpers;

    /**
     * ClientRestController constructor.
     */
    public function __construct()
    {
        $this->helpers['api'] = new ApiHelper();
        $this->helpers['client'] = new ClientHelper();
    }

    /**
     * Get All Client API
     *
     * @SWG\Get(
     *      path="/client",
     *      tags={"Client"},
     *      summary="Get all clients",
     *      description="Get all clients",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request)
    {
        $term = $request->input('term');

        $datas = $this->helpers['client']->all($term);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}
