<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\SkillHelper;

/**
 * Class TalentRestController
 * @package App\Http\Controllers\API
 */
class SkillRestController extends Controller
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
        $this->helpers['skill'] = new SkillHelper();
    }

    /**
     * Get All Talent API
     *
     * @SWG\Get(
     *      path="/skill",
     *      tags={"Skill"},
     *      summary="Get all skills",
     *      description="Get all skills",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request)
    {
    		$term = $request->input('term');

        $datas = $this->helpers['skill']->all($term);
        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Post(
     *      path="/skill",
     *      tags={"Skill"},
     *      summary="Add a skill",
     *      description="Add a skill",
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

        $datas = $this->helpers['skill']->add($name);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Delete(
     *      path="/skill/{id}",
     *      tags={"Skill"},
     *      summary="Delete a skill",
     *      description="Delete a skill",
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
    		$datas = $this->helpers['skill']->delete($id);

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

}
