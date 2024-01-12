<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\ResolutionHelper;

class ResolutionRestController extends Controller
{
    //
    /**
     * Helpers
     *
     * @var []
     */
    private $helpers;

    /**
     * ResolutionRestController constructor.
     */
    public function __construct()
    {
        $this->helpers['api'] = new ApiHelper();
        $this->helpers['resolution'] = new ResolutionHelper();
    }

    /**
     * Get All Resolution API
     *
     * @SWG\Get(
     *      path="/resolution",
     *      tags={"Resolution"},
     *      summary="Get all resolutions",
     *      description="Get all resolutions",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request)
    {
        $term = $request->input('term');

        $datas = $this->helpers['resolution']->all($term);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Post(
     *      path="/resolution",
     *      tags={"Resolution"},
     *      summary="Add a resolution",
     *      description="Add a resolution",
     *      @SWG\Parameter(
     *          name="name",
     *          description="",
     *          required=true,
     *          type="string",
     *          in="query"
     *      ),
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     */

    public function add(Request $request)
    {
    		$name = $request->input('name');

        $datas = $this->helpers['resolution']->add($name);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Delete(
     *      path="/resolution/{id}",
     *      tags={"Resolution"},
     *      summary="Delete a resolution",
     *      description="Delete a resolution",
     *      @SWG\Parameter(
     *          name="id",
     *          description="",
     *          required=true,
     *          type="string",
     *          in="path"
     *      ),
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     */

    public function delete($id, Request $request)
    {
    		$datas = $this->helpers['resolution']->delete($id);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}