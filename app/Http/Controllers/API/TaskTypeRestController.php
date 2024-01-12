<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\TaskTypeHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskTypeRestController extends Controller
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
        $this->helpers['taskType'] = new TaskTypeHelper();
    }

    /**
     * Get All TaskType API
     *
     * @SWG\Get(
     *      path="/task-type",
     *      tags={"TaskType"},
     *      summary="Get all TaskType",
     *      description="Get all TaskType",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request)
    {
        $term = $request->input('term');
        $type = $request->input('type');

        $datas = $this->helpers['taskType']->all($term, $type);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Post(
     *      path="/task-type",
     *      tags={"TaskType"},
     *      summary="Add a task type",
     *      description="Add a task type",
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

        $datas = $this->helpers['taskType']->add($name);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Delete(
     *      path="/task-type/{id}",
     *      tags={"TaskType"},
     *      summary="Delete a task type",
     *      description="Delete a task type",
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
    		$datas = $this->helpers['taskType']->delete($id);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}
