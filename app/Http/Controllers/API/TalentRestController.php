<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\TalentHelper;


/**
 * Class TalentRestController
 * @package App\Http\Controllers\API
 */
class TalentRestController extends Controller
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
        $this->helpers['talent'] = new TalentHelper();
    }

    /**
     * Get All Talent API
     *
     * @SWG\Get(
     *      path="/talent",
     *      tags={"Talent"},
     *      summary="Get all talents",
     *      description="Get all Talents",
     *      @SWG\Parameter(
     *          name="filter_alpha",
     *          description="Filter on the firstname first letter",
     *          required=false,
     *          type="string",
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="filter_name",
     *          description="search using name and firstname",
     *          required=false,
     *          type="string",
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="filter_job",
     *          description="Search using job ",
     *          required=false,
     *          type="string",
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="filter_skills",
     *          description="Filter on Skils using skills id",
     *          required=false,
     *          type="string",
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="itemsPerPage",
     *          description="Item to show per page",
     *          required=false,
     *          type="integer",
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="page",
     *          description="page to show",
     *          required=false,
     *          type="integer",
     *          in="query"
     *      ),
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request)
    {
        switch ($request->input('mode')) {
            case 'search':
                $datas = $this->helpers['talent']->search($request->input('term'), $request->input('workspace'));
                break;
            case 'contact':
                $workspace = $request->input('workspace') ?? '';
                $datas = $this->helpers['talent']->getContact($workspace);
                break;

            default:
                $datas = $this->helpers['talent']->all($request->all());
                break;
        }
        $output = $this->helpers['api']->output($datas);
        return response()->json($output);
    }

    /**
     * Get One Talent API
     *
     * @SWG\Get(
     *      path="/talent/{id}",
     *      tags={"Talent"},
     *      summary="Get all talents",
     *      description="Get all Talents",
     *      @SWG\Parameter(
     *          name="id",
     *          description="Talent ID",
     *          required=true,
     *          type="string",
     *          in="path"
     *      ),
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function get(int $id)
    {
        $datas = $this->helpers['talent']->get($id);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    //
    public function addOrUpdate(Request $request)
    {
        $params = $request->all();
        $header = $request->header();
        $params = array_merge($params, $header);

        $datas = $this->helpers['talent']->addOrUpdate($params);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    //
    public function updateProfile(Request $request)
    {
        $params = $request->all();
        $header = $request->header();
        $params = array_merge($params, $header);

        $datas = $this->helpers['talent']->updateProfile($params);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    //
    public function delete($id)
    {
        $datas = $this->helpers['talent']->delete($id);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }
}
