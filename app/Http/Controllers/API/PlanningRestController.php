<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\PlanningHelper;
use Illuminate\Http\Request;

class PlanningRestController extends Controller
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
        $this->helpers['planning'] = new PlanningHelper;
    }

    //
    public function get(Request $request)
    {
        if ($request->type == 'PLANNING') {
            $datas = $this->helpers['planning']->getPlanning($request->all());
        } else {
            $datas = $this->helpers['planning']->get($request->all());
        }
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    public function addGroup(Request $request)
    {
        $datas = $this->helpers['planning']->addOrUpdateGroup($request->all());
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    public function getGroups(Request $request)
    {
        $workspace = $request->input('workspace') ?? '';
        $datas = $this->helpers['planning']->getGroups($request->all(), $workspace);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
    public function deleteGroup(Request $request) 
    {
        $datas = $this->helpers['planning']->deleteGroup($request->all());
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}
