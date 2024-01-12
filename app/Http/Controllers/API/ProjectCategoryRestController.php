<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\ProjectCategoryHelper;

class ProjectCategoryRestController extends Controller
{
    /**
     * Helpers
     *
     * @var []
     */
    private $helpers;

    /**
     * ProjectCategoryRestController constructor.
     */
    public function __construct()
    {
        $this->helpers['api'] = new ApiHelper();
        $this->helpers['projectCategory'] = new ProjectCategoryHelper();
    }

    /**
     * Get All ProjectCategory API
     *
     * @SWG\Get(
     *      path="/project-category",
     *      tags={"ProjectCategory"},
     *      summary="Get all project categories",
     *      description="Get all project Categories",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request)
    {
        $term = $request->input('term');

        $datas = $this->helpers['projectCategory']->all($term);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Post(
     *      path="/project-category",
     *      tags={"ProjectCategory"},
     *      summary="Add a project category",
     *      description="Add a project category",
     *      @SWG\Parameter(
     *          name="name",
     *          description="",
     *          required=true,
     *          type="string",
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="color",
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
    		$color = $request->input('color');

        $datas = $this->helpers['projectCategory']->add($name, $color);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Delete(
     *      path="/project-category/{id}",
     *      tags={"ProjectCategory"},
     *      summary="Delete a project category",
     *      description="Delete a project category",
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
    		$datas = $this->helpers['projectCategory']->delete($id);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}
