<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\ExportHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Events\ExportAdded;

class ExportRestController extends Controller
{
    /**
     * Helpers
     * @var [Array]
     */
    private $helpers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->helpers['api'] = new ApiHelper;
        $this->helpers['export'] = new ExportHelper();
    }

    /**
     * @SWG\Get(
     *      path="/export",
     *      tags={"Export"},
     *      summary="Get all exports",
     *      description="Get all exports",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     * @param Request $request
     * @return JsonResponse
     */

    public function all(Request $request)
    {
        $datas = $this->helpers['export']->all($request->all());

        $output = $this->helpers['api']->output(['exports' => $datas]);

        return response()->json($output);
    }

    /**
     * @SWG\Get(
     *      path="/export/{id}",
     *      tags={"Export"},
     *      summary="Get export detail",
     *      description="Get export detail",
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

    public function get($id)
    {
        $datas = $this->helpers['export']->get($id);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    public function addOrUpdate(Request $request)
    {
        $params = $request->all();
        $header = $request->header();
        $params = array_merge($params, $header);

        $datas = $this->helpers['export']->addOrUpdate($params);
        event(new ExportAdded());
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    public function delete(Request $request)
    {
    	$export_ids = $request->input('exports');

        $datas = $this->helpers['export']->delete($export_ids);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    public function patch($id, Request $request)
    {
        $datas = $this->helpers['export']->patch($id, $request->all());
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    public function updateStatus(Request $request) 
    {
        $datas = $this->helpers['export']->updateStatus($request->all());
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}
