<?php

namespace App\Http\Controllers\API;

use App\Events\AppointmentEvent;
use App\Events\AppointmentDeletedEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\AppointmentHelper;
use Illuminate\Support\Facades\Log;
use Swagger\Annotations as SWG;
use \DB;

class AppointmentRestController extends Controller
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
        $this->helpers['appointment'] = new AppointmentHelper;
    }

    /**
     * @SWG\Get(
     *      path="/appointment",
     *      tags={"Appointment"},
     *      summary="Get all appointments",
     *      description="Get all appointments",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     * @param Request $request
     * @return JsonResponse
     */

    public function all(Request $request)
    {
        $datas = $this->helpers['appointment']->all($request->all());

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Get(
     *      path="/appointment/{id}",
     *      tags={"Appointment"},
     *      summary="Get appointment detail",
     *      description="Get appointment detail",
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
        $datas = $this->helpers['appointment']->get($id);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    public function getLocation(Request $request)
    {
        $datas = $this->helpers['appointment']->getLocation($request);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * Update the specified user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function addOrUpdate(Request $request)
    {
        $params = $request->all();
        $header = $request->header();
        $params = array_merge($params, $header);
        $datas = $this->helpers['appointment']->addOrUpdate($params);

        $output = $this->helpers['api']->output($datas);

        event(new AppointmentEvent($output));

        return response()->json($output);
    }

    public function delete($id)
    {
        $beforeDeleteDatas = $this->helpers['appointment']->get($id);
        $output = $this->helpers['api']->output($beforeDeleteDatas);

        $this->helpers['appointment']->delete($id);

        event(new AppointmentDeletedEvent($output));

        return response()->json($output);
    }
    public function Update($id, Request $request)
    {
        $appointmentHelper = new AppointmentHelper;
        return $appointmentHelper->put($id, $request);
    }

    public function viewed(Request $request){
        $isViewed = false;
        $params = $request->input('params');
        if ($params) {
            if (DB::table('appointment_talent')->where('appointment_id', $params['appointmentId'])->where('user_id', $params['userId'])->update(['viewed' => 1])) {
                $isViewed = true;
            }
        }

        return response()->json($isViewed);
    }
}
