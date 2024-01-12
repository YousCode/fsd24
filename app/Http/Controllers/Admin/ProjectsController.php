<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Embed\Embed;
use App\Http\Helpers\ProjectHelper;
use App\Http\Helpers\ExportHelper;
use App\Http\Helpers\ApiHelper;
use App\Events\FileAdded;

use App\Models\Project;
use App\Models\Workspace;
use App\Models\Contact;
use App\ContactStatusEnum;
use App\User;

class ProjectsController extends Controller
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
    	$this->helpers['export'] = new ExportHelper;
    }

 	public function index()
 	{
		$workspaceOwner = ['id' => false];
		$userWorkspaceId = auth()->user()->getCurrentWorkspaceAttribute() ?? false;
		if ($userWorkspaceId) {
			$workspace = Workspace::findOrFail($userWorkspaceId);
			if ($workspace) {
				$workspaceOwner['id'] = $workspace->owner_id;
			}
		}

		//$kanban = Project::select(['id', 'name', 'category_id', 'date_deadline', 'updated_at']);
		//$closed = Project::select(['id', 'name', 'category_id', 'date_deadline']);
		if (auth()->user() && auth()->user()->getCurrentWorkspaceAttribute() && auth()->user()->getCurrentWorkspaceAttribute() > 0) {
			//$kanban = $kanban->where('workspace_id', auth()->user()->getCurrentWorkspaceAttribute());
			//$closed = $closed->where('workspace_id', auth()->user()->getCurrentWorkspaceAttribute());
		}
		//$closed = $closed->orderBy('projects.name', 'asc');
		//$kanban = $kanban->orderBy('projects.updated_at', 'desc')->get();
		
		//$kanban = $this->helpers['project']->orderKanban($kanban, auth()->user()->id);
 		return view('pages.tpl_projects', ['workspaceOwner' => json_encode($workspaceOwner)]);
 	}

 	public function details(Request $request, $id)
	{
        // Get datas
		$userId = false;
		if (auth()->user() && auth()->user()->getCurrentWorkspaceAttribute() && auth()->user()->getCurrentWorkspaceAttribute() > 0) {
			$userId = auth()->user()->id;
		}
 		$project = Project::where('id', $id)->first();
		if (!$project) {
			$project = Project::where('sharing_token', $id)->first();
			if (!$project) {
				return redirect("login");
			}
		}
 		$conversation = false;
 		if (!empty($project->proposition->conversation)) {
 		    $conversation = $project->proposition->conversation;
        }
		$userWorkspaceId = ($project->id == 1 || !auth()->user()) ? 1 : auth()->user()->getCurrentWorkspaceAttribute();
		$projectWorkspaceId = $project->workspace_id;
		if (!$project || (auth()->user() && $projectWorkspaceId != $userWorkspaceId && $project->id != 1) && !is_null(auth()->user())) {
			return redirect("/");
		}
		if (auth()->user() && $project->category->name == "mission-explorer") {
			$conversationId = $conversation->id ?? '';
			$explorerMessenger = route('app_explorer_messenger') . '?conversationId=' . $conversationId;
			return redirect($explorerMessenger);
		}

		$path = [];

        if (!empty($request->session()->get('_previous')['url'])) {
            $intented = $request->session()->get('_previous')['url'];
            if ($intented) {
                $intented = parse_url($intented);
                if (!empty($intented['path'])) {
                    $path['previousUrl'] = $intented['path'];
                }
            }
        }

 		return view('pages.tpl_project_details', [
 			'project' => $project,
            'conversation' => $conversation ?? [],
			'user' => \Auth::user() ?? json_encode(['isAuth' => false]),
			'is_client' => (\Auth::user() && \Auth::user()->hasRole('client')) ? json_encode(['isClient' => \Auth::user()->hasRole('client')]) : json_encode(['isClient' => false]),
			'talents' => ($project) ? $project->getTalents() : [],
			'userAuth' => \Auth::user(),
			'path' => json_encode($path),
 		]);
 	}

	public function exports($id)
	{
		$type = ["type" => "ADD"];
		$exportId = false;
		$export = [];
		if (!empty($_GET['export_id'])) {
			$type = ["type" => "EDIT"];
			$exportId = $_GET['export_id'];
			$export = $this->helpers['export']->get($exportId);
		}
		$exports = $this->helpers['export']->all(['project_id' => $id]);
		$project = Project::where('id', $id)->first();
		if (\Auth::user() && \Auth::user()->getCurrentWorkspaceAttribute() && $project && $project->workspace_id && ($project->workspace_id != \Auth::user()->getCurrentWorkspaceAttribute())) {
			return redirect('/');
		}
		return view('pages.tpl_project_details_exports', [
			'project' => json_encode($project),
			'exports' => json_encode($exports),
			'export' => json_encode($export),
			'type' => json_encode($type),
			'user' => \Auth::user() ?? json_encode(['isAuth' => false]),
			'is_client' => (\Auth::user() && \Auth::user()->hasRole('client')) ? json_encode(['isClient' => \Auth::user()->hasRole('client')]) : json_encode(['isClient' => false]),
		]);
	}

	 //
	 public function addFiles($id, Request $request)
	 {
	   $files = $request->input('files');
	   $messageId = false;
	   $contactId = false;
	   $isShared = false;
	   if ($request->input('messageId') > 0) {
		 $messageId = $request->input('messageId');
	   }
	   if ($request->input('contactId') > 0) {
		$contactId = $request->input('contactId');
	  }
	  if ($request->input('isShared')) {
		$isShared = $request->input('isShared');
	  }
	   $user = \Auth::user() ?? false;
	   $datas = $this->helpers['project']->addFiles($id, $files, $messageId, $user, $contactId, $isShared);
	   $output = $this->helpers['api']->output($datas);
	   event(new FileAdded());

	   return response()->json($output);
	 }

	 public function createContact(Request $request) {
        $email = $request->input('email');
        if (!empty($email)) {
            $contact = Contact::where('email', $email)->first();
            if (!empty($contact)) {
                return response()->json([
                    "success" => true,
                    "message" => "Contact already added",
					"contactId" => $contact->id
                ]);
            }
        }
        $contact = new Contact();
        $contact->lastname = '';
        $contact->firstname = '';
        $contact->phone = '';
        $contact->email = $email ?? '';
        $contact->access_token = $this->UnlockAccess();
        $contact->contact_status = '';
        $contact->status = ContactStatusEnum::WAITING;

        try {
            $contact->save();
            return response()->json([
                "success" => true,
                "message" => "Contact has been added",
				"contactId" => $contact->id
            ]);

        } catch (ApiException $e) {
            throw new \Exception($e->getMessage());
        }
    }

	protected function UnlockAccess(): string
    {
        $hash = Hash::make('kolab');
        return str_replace("/",".","$hash");
    }
}
