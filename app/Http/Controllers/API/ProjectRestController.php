<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Helpers\ApiHelper;
use App\Http\Helpers\ProjectHelper;
use App\Events\FileAdded;
use App\Events\MessageAdded;
use App\Events\DriveDeleted;
use App\Events\FileDeleted;
use App\Events\UpdateProjectsEvent;

use App\Models\Project;
use App\Models\Task;
use App\ProjectStep;
use \DB;

class ProjectRestController extends Controller
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
      $this->helpers['project'] = new ProjectHelper;
    }

    /**
     * @SWG\Get(
     *      path="/project",
     *      tags={"Project"},
     *      summary="Get all projects",
     *      description="Get all projects",
     *      @SWG\Response(response=200, description="Successful operation"),
     * )
     * @param Request $request
     * @return JsonResponse
     */

    public function all(Request $request)
    {
        switch ($request->input('mode')) {
            case 'search':
                $datas = $this->helpers['project']->search($request);
                break;

            default:
                $datas = $this->helpers['project']->all($request->all());
                break;
        }

        $output = $this->helpers['api']->output($datas);

        return response()->json($output);
    }

    /**
     * @SWG\Get(
     *      path="/project/{id}",
     *      tags={"Project"},
     *      summary="Get project detail",
     *      description="Get project detail",
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
      $datas = $this->helpers['project']->get($id);

      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    public function addOrUpdate(Request $request)
    {
      $params = $request->all();
      $header = $request->header();
      $params = array_merge($params, $header);
      $datas = $this->helpers['project']->addOrUpdate($params);
      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    public function conversation(Request $request)
    {
       $params = $request->all();

       return $this->helpers['project']->createProjectConversation($params);
    }

    //
    public function delete($id)
    {
      $datas = $this->helpers['project']->delete($id);
      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    //
    public function getPlanning(Request $request)
    {
      $datas = $this->helpers['project']->getPlanning($request);
      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    //
    public function addFiles($id, Request $request)
    {
    	$files = $request->input('files');
      $messageId = false;
      if ($request->input('messageId') > 0) {
        $messageId = $request->input('messageId');
      }
      $user = \Auth::user();
    	$datas = $this->helpers['project']->addFiles($id, $files, $messageId, $user);
      $output = $this->helpers['api']->output($datas);
      event(new FileAdded());

      return response()->json($output);
    }

    //
    public function getFiles($id)
    {
    	$datas = $this->helpers['project']->getFiles($id);
      $output = $this->helpers['api']->output($datas);

      return response()->json($output);
    }

    //
    public function deleteFiles(Request $request)
    {
    	$file_ids = $request->input('files');

      $datas = $this->helpers['project']->deleteFiles($file_ids);
      $output = $this->helpers['api']->output($datas);
      event(new FileDeleted());

      return response()->json($output);
    }

    public function deleteDrives(Request $request)
    {
    	$drive_ids = $request->input('drives');

      $datas = $this->helpers['project']->deleteDrives($drive_ids);
      $output = $this->helpers['api']->output($datas);
      event(new DriveDeleted());

      return response()->json($output);
    }


	public function kanban(Request $request) {
    $all = $request->input('all') ?? false;
		$kanban = DB::table('projects')->select(['projects.id', 'projects.name', 'projects.created_at', 'category_id', 'date_deadline', 'projects.updated_at', 'project_categories.name as categoryName', 'explorer_mission_propositions.freelance_id as freelanceId', 'explorer_mission_propositions.client_id as clientId'])->join('project_categories', 'project_categories.id', '=', 'projects.category_id')->join('explorer_mission_propositions', 'explorer_mission_propositions.kolab_project_id', '=', 'projects.id');
		//$closed = Project::select(['id', 'name', 'category_id', 'date_deadline', 'updated_at']);
		if (auth()->user() && auth()->user()->getCurrentWorkspaceAttribute() && auth()->user()->getCurrentWorkspaceAttribute() > 0) {
			$kanban = $kanban->where('workspace_id', auth()->user()->getCurrentWorkspaceAttribute());
			//$closed = $closed->where('workspace_id', auth()->user()->getCurrentWorkspaceAttribute());
		}
    if (!empty($request['filter_name'])) {
      $kanban->where('projects.name', 'like', '%' . $request['filter_name'] . '%');
    }
    if (!empty($request['filter_categories'])) {
      $kanban->whereIn('projects.category_id', $request['filter_categories']);
    }
    // Order
    if (!empty($request['filter_sortby'])) {
      if($request['filter_sortby'] == 'SORT_RECENT_TO_OLDER'){
        $kanban->orderBy('projects.date_deadline', 'DESC');
      } else {
        $kanban->orderBy('projects.date_deadline', 'ASC');
      }
    } else {
      $kanban = $kanban->orderBy('projects.updated_at', 'desc');
    }
    //$closed = $closed->orderBy('projects.name', 'asc');
    $kanban = $kanban->groupBy('projects.id');
    $kanban = $this->helpers['project']->orderKanban($kanban, auth()->user()->id);

    return response()->json($kanban);
	}

  public function updateProjectStep(Request $request) {
    $from = $request->input('from') ?? false;
    $to = $request->input('to') ?? false;
    $projectId = $request->input('projectId') ?? false;
    if ($projectId && $from && $from == 'in_progress' && $to && $to == 'closed') {
      $tasks = Task::where('project_id', $projectId)->where('status', 'PROGRESS')->get();
      $project = Project::findOrFail($projectId);
      try {
        if ($tasks) {
          foreach ($tasks as $task) {
            $task->status = ProjectStep::CLOSED;
            $task->closed_at = new \DateTime();
            $task->save();
          }
          if ($project) {
            $project->updated_at = new \DateTime();
            $project->save();
          }
          event(new UpdateProjectsEvent());
        }
      } catch(\Exception $e) {
        Log::channel('single')->info($e->getMessage());
        throw new \Exception($e->getMessage());
      }

      return response()->json(['projectId' => $projectId]); 
    }

    return response()->json(['projectId' => false]); 
  }
}
