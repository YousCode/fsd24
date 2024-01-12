<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\DashboardHelper;
use App\Http\Helpers\TaskHelper;
use Illuminate\Http\Request;

class DashboardRestController extends Controller
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
        $this->helpers['dashboard'] = new DashboardHelper();
        $this->helpers['task'] = new TaskHelper();
    }

    //
    public function get(Request $request, $id)
    {
    	$params = $request->all();
        $header = $request->header();
        $params = array_merge($params, $header);

        $datas = $this->helpers['dashboard']->get($id, $params);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    public function getTaskToClose($userId) {
        $datas = $this->helpers['task']->tasksToClose($userId);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}
