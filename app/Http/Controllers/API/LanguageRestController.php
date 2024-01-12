<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\LanguageHelper;

class LanguageRestController extends Controller
{
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
        $this->helpers['language'] = new LanguageHelper();
    }

    /**
     * Get All Language API
     *
     * @SWG\Get(
     *      path="/language",
     *      tags={"Language"},
     *      summary="Get all languages",
     *      description="Get all languages",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request)
    {
        $term = $request->input('term');

        $datas = $this->helpers['language']->all($term);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Post(
     *      path="/language",
     *      tags={"Language"},
     *      summary="Add a language",
     *      description="Add a language",
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

        $datas = $this->helpers['language']->add($name);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Delete(
     *      path="/language/{id}",
     *      tags={"Language"},
     *      summary="Delete a language",
     *      description="Delete a language",
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
    		$datas = $this->helpers['language']->delete($id);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}