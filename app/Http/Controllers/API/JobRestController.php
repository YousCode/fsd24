<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\JobHelper;

/**
 * Class TalentRestController
 * @package App\Http\Controllers\API
 */
class JobRestController extends Controller
{
    /**
     * Helpers
     *
     * @var []
     */
    private $helpers;

    /**
     * TalentRestController constructor.
     */
    public function __construct()
    {
        $this->helpers['api'] = new ApiHelper();
        $this->helpers['job'] = new JobHelper();
    }

    /**
     * Get All Talent API
     *
     * @SWG\Get(
     *      path="/job",
     *      tags={"Job"},
     *      summary="Get all jobs",
     *      description="Get all jobs",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request)
    {
        $term = $request->input('term');

        $datas = $this->helpers['job']->all($term);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Post(
     *      path="/job",
     *      tags={"Job"},
     *      summary="Add a job",
     *      description="Add a job",
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

        $datas = $this->helpers['job']->add($name);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Delete(
     *      path="/job/{id}",
     *      tags={"Job"},
     *      summary="Delete a job",
     *      description="Delete a job",
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
    		$datas = $this->helpers['job']->delete($id);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

}
