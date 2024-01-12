<?php

namespace App\Http\Helpers;

use Exception;
use Illuminate\Database\Eloquent\Collection;

/**
 * @SWG\Swagger(
 *     basePath="/api",
 *     schemes={"https"},
 *     host=L5_SWAGGER_CONST_HOST,
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="",
 *         description="",
 *         @SWG\Contact(
 *             email=""
 *         ),
 *     )
 * )
 */
class ApiHelper
{

    /**
     * Start time request
     * @var float
     */
    public $startTime;

    /**
     * End time request
     * @var float
     */
    public $endTime;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Output datas
     * @param Boolean $success Status
     * @param Object $datas Datas
     * @return Object  Array or Object
     */
    public function output($datas, $success = true)
    {
        $this->endTime = microtime(true);

        $output = new \StdClass;
        $output->success = $success;
        $output->datas = $datas;
        $output->total = (is_array($datas) || $datas instanceof Collection) ? count($datas) : 1;
        $output->requestTime = ($this->endTime - $this->startTime);

        return $output;
    }

    /**
     * Todo : Error response
     * @return []
     */
    public function error($message)
    {
        $this->endTime = microtime(true);

        $output = new \StdClass;
        $output->success = false;

        if (is_array($message)) {
            $output->datas = $message;
        } else {
            $output->datas = ['message' => $message];
        }


        $output->total = 0; // Todo
        $output->requestTime = ($this->endTime - $this->startTime);

        //_error($message);
        response()->json($output)->send();
        die;
    }

    // -- USEFUL

    /**
     * Check if a parameter exist and if it isn't null
     *
     * @param $key string Key to check
     * @param $array array to check
     *
     * @return bool
     */
    public function checkParameterExistAndNotEmpty($key, $array)
    {
        return isset($array[$key]) && !empty($array[$key]);
    }

    /**
     * Check if all required parameters for Talent creation or update are provided
     *
     * @param $params
     * @param $mandatoryParameters
     * @throws Exception
     */
    public function checkParameters($params, $mandatoryParameters)
    {
        foreach ($mandatoryParameters as $mandatoryParameter) {
            if (!isset($params[$mandatoryParameter])) {
                throw new Exception("$mandatoryParameter field is missing");
            }
        }
    }
}
