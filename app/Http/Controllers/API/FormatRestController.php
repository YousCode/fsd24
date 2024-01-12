<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\FormatHelper;

class FormatRestController extends Controller
{
    //
    /**
     * Helpers
     *
     * @var []
     */
    private $helpers;

    /**
     * FormatRestController constructor.
     */
    public function __construct()
    {
        $this->helpers['api'] = new ApiHelper();
        $this->helpers['format'] = new FormatHelper();
    }

    /**
     * Get All Format API
     *
     * @SWG\Get(
     *      path="/format",
     *      tags={"Format"},
     *      summary="Get all formats",
     *      description="Get all formats",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request)
    {
        $term = $request->input('term');

        $datas = $this->helpers['format']->all($term);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}