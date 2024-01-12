<?php

namespace App\Http\Helpers;

use App\ProjectStep;
use App\Notifications\AddMedia;

use App\Models\ExplorerDrive;
use App\Models\ExplorerMission;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionConversationMessage;
use App\Models\ExplorerMissionProposition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use DB;
use DateTime;
use Exception;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ExplorerDriveLink;
use App\Models\Task;
use App\Models\TaskDates;
use App\User;

use App\Http\Helpers\TaskHelper;
use App\Http\Helpers\NotificationHelper;

/**
 * Class ProjectHelper
 * @package App\Http\Helpers
 */
class ProjectHelper
{

    /**
     * Get all projects
     * @param $request
     * @return Project[]|Collection [Object]  Project
     */
    public function all($request = [])
    {
      $projects = Project::query()->select(['*', DB::raw('YEAR(date_deadline) year, MONTH(date_deadline) month')]);
      
      if (_helper('api')->checkParameterExistAndNotEmpty('filter_name', $request)) {
        $projects->where('name', 'like', '%' . $request['filter_name'] . '%');
      }
      
      if (_helper('api')->checkParameterExistAndNotEmpty('filter_projects_id', $request)) {
          $projects->whereIn('id', [$request['filter_projects_id']]);
      }

      if (_helper('api')->checkParameterExistAndNotEmpty('filter_categories', $request)) {
          $projects->whereIn('category_id', $request['filter_categories']);
      }

      if (_helper('api')->checkParameterExistAndNotEmpty('filter_status', $request)) {
      	if($request['filter_status'] == 'STATUS_PROGRESS'){
          $projects->where(DB::raw('DATE(date_deadline)'), '>=', Carbon::now()->setTimezone(config('timezone'))->format('Y-m-d'));
        } elseif($request['filter_status'] == 'STATUS_CLOSED') {
        	$projects->where(DB::raw('DATE(date_deadline)'), '<', Carbon::now()->setTimezone(config('timezone'))->format('Y-m-d'));
        }
      }

      if (_helper('api')->checkParameterExistAndNotEmpty('itemsPerPage', $request)) {
          $projects->limit($request['itemsPerPage']);
      }

      if (_helper('api')->checkParameterExistAndNotEmpty('page', $request)) {
          $projects->skip(($request['page'] - 1) * $request['page']);
      }

      // Order
      if (_helper('api')->checkParameterExistAndNotEmpty('filter_sortby', $request)) {
      	if($request['filter_sortby'] == 'SORT_RECENT_TO_OLDER'){
      		$projects->orderBy('date_deadline', 'DESC');
      	} else {
      		$projects->orderBy('date_deadline', 'ASC');
      	}
      }
      $projects->where('workspace_id', '=', $request['workspace'])->orWhere('id', 1);
      
      return $projects->get()
          ->groupBy(['year', 'month']);
    }

    /**
     * Get project
     * @param  [Integer] $id Project id
     * @return Project|Builder|Model|object|null [Object]  Project
     */
    public function get($id)
    {
      return Project::where('id', $id)->first();
    }

    /**
     * [search description]
     * @param  [type] $term [description]
     * @return [type]       [description]
     */
    public function search($request)
    {
      $workspace = $request->input('workspace') ?? false;
      $term = $request->input('term') ?? false;
      $noExplorer = $request->input('noExplorer') ?? false;
      $userId = $request->input('userId') ?? false;
      if ($workspace) {
        $projects = Project::where('workspace_id', '=', $workspace);
        if ($term) {
          $projects = $projects->join('explorer_mission_propositions', 'explorer_mission_propositions.kolab_project_id', '=', 'projects.id');
          $projects = $projects->where('name', 'LIKE', "%$term%");
          $projects = $projects->groupBy('projects.id');
        }
        if ($noExplorer) {
          $projects = $projects->whereNot('category_id', 9); // 9 is mission-explorer category ID
        }

        $projects->orderBy('name', 'ASC');

        $projects = $projects->get();
        if ($term && $userId) {
          $searchProjects = [];
          foreach($projects as $project) {
            if ($project->category_id != 9 || ($project->category_id == 9 && ($project->freelance_id == $userId || $project->client_id == $userId))) {
              $searchProjects[] = $project;
            }
          }

          return $searchProjects;
        }

        return $projects;
      }

      return false;
    }

    /**
     * Add or update a project
     * @param
     * @return mixed [Object]  Project
     */
    public function addOrUpdate($params)
    {
      $this->helpers['notification'] = new NotificationHelper;

      $projectParams = $params['project'];
      $mandatoryParameters = [];

      // Check if all parameters are provided
      try {
        _helper('api')->checkParameters($projectParams, $mandatoryParameters);
      } catch (Exception $e) {
        // Return the exception message if error
        _helper('api')->error($e->getMessage());
      }
      if (isset($projectParams['id']) && $projectParams['id']) {
      	$action = 'UPDATE';
        $project = Project::find($projectParams['id']);
        $project->updated_by = (int)$params['user'][0];
      } else {
      	$action = 'ADD';
        $project = new Project();
        $project->created_by = (int)$params['user'][0];
		    $project->updated_by = (int)$params['user'][0];
      }
      $production = '';
      $clientPhone = '';
      $clientMail = ''; 
      $responsableName = ''; 
      $responsablePhone = ''; 
      $responsableMail = '';
      if ($action == "UPDATE") {
        $production = $project->production;
        $clientPhone = $project->client_phone;
        $clientMail = $project->client_email;
        $responsablePhone = $project->responsable_name;
        $responsablePhone = $project->responsable_phone;
        $responsableMail = $project->responsable_email;
      }
      $project->name = $projectParams['name'] ?? $project->name;
      $project->production = $projectParams['production'] ?? $production;
      $project->client_id = $this->findOrCreateClient($params);
      $project->client_phone = $projectParams['client_phone'] ?? $clientPhone;
      $project->client_email = $projectParams['client_email'] ?? $clientMail;
      $project->responsable_name = $projectParams['responsable_name'] ?? $responsableName;
      $project->responsable_phone = $projectParams['responsable_phone'] ?? $responsablePhone;
      $project->responsable_email = $projectParams['responsable_email'] ?? $responsableMail;
      $project->category_id = $projectParams['category_id'] ?? $project->category_id;
      $project->date_deadline = Carbon::parse($projectParams['date_deadline'] ?? $project->date_deadline);
      if ($action == 'ADD') {
        $project->sharing_token = substr(md5(uniqid($project->name)), 0, 8);
      } else {
        $project->sharing_token = $project->sharing_token;
      }
      $project->workspace_id = $projectParams['workspace_id'] ?? $project->workspace_id;

      try {
        $project->save();

        $icon = $action == 'ADD' ? '🆕' : '✅';
        $action = $action == 'ADD' ? 'créé' : 'modifié';
        $this->helpers['notification']->save("$icon Le projet ".$project->name." a bien été ".$action."", $params['user'][0], 'PROJET', $project->id);
      } catch (Exception $e) {
        _helper('api')->error($e->getMessage());
      }

      $project->talents()->detach();
      if (isset($projectParams['talents_id']) && is_array($projectParams['talents_id'])) {
        $project->talents()->sync($projectParams['talents_id']);
      }

        try {
        $project->save();
      } catch (Exception $e) {
        _helper('api')->error($e->getMessage());
      }

      return $project;
    }

    /**
     * Delete One project
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
      $project = Project::find($id);
		try {
		  $project->talents()->detach();
		  $tasks = $project->task();
		  if ($tasks) {
			foreach($tasks as $task) {
			  $task->delete();
			}
		  }
		  $proposition = $project->proposition()->first();
		  $propositionId = $proposition->id;
		  $drive = \App\Models\ExplorerDrive::where('id_proposition', $propositionId)->first();
		  $driveId = $drive->id;
		  $projectConversation = \App\Models\ExplorerMissionConversation::where('id_proposition', $propositionId)->first();
		  $conversationid = $projectConversation->id;

		  $driveLinks = $drive->driveLinks()->getResults();
		  if (!empty($driveLinks)) {
			foreach($driveLinks as $link) {
			  $link->delete();
			}
		  }

		  $messages = $projectConversation->messages()->getResults();
		  if (!empty($messages)) {
        foreach($messages as $message) {
          $message->delete();
        }
		  }

		  $drive->delete();
		  $projectConversation->delete();
		  $proposition->delete();
      $project->delete();
		} catch (Exception $e) {
		  _helper('api')->error($e->getMessage());
		}

      return true;
    }

    /**
     * [getPlanning description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getPlanning($params)
    {
      // Helpers
  	  $taskHelper = new TaskHelper();
  	  $id = $params['id'];
  	  // Project tasks
      $tasks = $taskHelper->getBase();
      $projectTasks = $tasks->where('tasks.project_id', $id)
            //->leftJoin('task_dates', 'tasks.id', '=', 'task_dates.task_id')
       			->orderBy('start_date')
  					->get();

      // Project start and end informations
      if (!empty($projectTasks)) {
        if (!empty($projectTasks[0])) {
          $result['project_start'] = $projectTasks[0]->start_date;
        }
        if (!empty($projectTasks->last())) {
          $result['project_end'] = Carbon::parse($projectTasks->last()->end_date)->add(1, 'day');
        }
      }

      setlocale(LC_TIME, 'fr_FR');
      date_default_timezone_set('Europe/Paris');

      try {
          // Create a Date Interval Iterator to create the planning task array
          /*$period = new DatePeriod(
              new DateTime($result['project_start']),
              new DateInterval('P1D'),
              new DateTime($result['project_end'])
          );*/

          if (!isset($params['date_month']) || !isset($params['date_year'])) {
              $params['date_month'] = date('m');
              $params['date_year'] = date('Y');
          }

          $period = $this->getDatePeriod($params['date_month'], $params['date_year']);

          $tasksDate = DB::table('task_dates')
            ->where('task_dates.project_id', $id)
            ->orderBy('task_dates.start_date')
            ->get();
          // Populating the Planning Array
          $projectTaskId = false;
          foreach ($period as $date) {

        		$_date = $date->format('Y-m-d');

            // Initializing empty array for the date in case there is no task at the date
            $result['project_tasks'][$_date] = [];

            foreach ($projectTasks as $key => $projectTask) {
              if ($projectTask->id == $projectTaskId) {
                //continue;
              }
              $dateStart = new DateTime($projectTask->start_date);
              $dateEnd = new DateTime($projectTask->end_date);
              $datePlanning = new DateTime($_date);

                //if (($datePlanning->format('Y-m-d') == $dateStart->format('Y-m-d')) && ($datePlanning->format('Y-m-d') == $dateEnd->format('Y-m-d'))) {
                if (($datePlanning->format('Y-m-d') >= $dateStart->format('Y-m-d')) && ($datePlanning->format('Y-m-d') <= $dateEnd->format('Y-m-d'))) {
                    $task = Task::where('id', $projectTask->task_id)->first();
                    //$tasks = TaskDates::where('task_id', $task->id)->orderBy('start_date')->get();

                    $taskStartDate = false;
                    $taskEndDate = false;
                    /*if (!empty($tasks[0])) {
                      $taskStartDate = $tasks[0]->start_date;
                    }
                    if (count($tasks) && count($tasks) > 0) {
                      $tasksDateLength = count($tasks) - 1;
                      if (!empty($tasks[$tasksDateLength])) {
                        $taskEndDate = $tasks[$tasksDateLength]->start_date;
                      }
                    }*/
                    $taskStartDate = $projectTask->start_date;
                    $taskEndDate = $projectTask->end_date;
                    if (!empty($taskStartDate) && !empty($taskEndDate)) {
                      $taskStartDate = new DateTime($taskStartDate);
                      $taskEndDate = new DateTime($taskEndDate);
                    }
                    $startDateMonth = $taskStartDate->format('n');
                    $endDateMonth = $taskEndDate->format('n');
                    $startDateFormat = false;
                    if ($startDateMonth == $endDateMonth) {
                      $startDateFormat = 'd';
                    } else {
                      $startDateFormat = 'd F';
                    }
                    $taskStartDate = $taskStartDate->format($startDateFormat);
                    $taskEndDate = $taskEndDate->format('d F');

                    $closedAt = $projectTask->closed_at;
                    $closedAt = new DateTime($closedAt);
                    $closedAt = $closedAt->format('d F');
                    //$projectTask->talents = $task->talents ?? false;
                    $projectTask->task_type = $projectTask->taskTypes[0]->name ?? '';
                    $projectTask->class = 'task-' . $projectTask->project_id . '-' . $projectTask->id;
                    //$projectTask->taskDateId = $projectTask->id;
                    //$projectTask->taskTypes = $task->taskTypes;
                    $projectTask->firstname = $projectTask->talentName;
                    $projectTask->created_by = $projectTask->owner->name ?? '';
                    $projectTask->startDate = $taskStartDate;
                    $projectTask->endDate = $taskEndDate;
                    $projectTask->closedAt = $closedAt;
                    //$projectTask->status = $task->status;
                    $result['project_tasks'][$_date][] = $projectTask;

                    $projectTaskId = $projectTask->id;
                }
            }
          }
        } catch (Exception $e) {
          _helper('api')->error($e->getMessage());
        }

        // Tasks symmary
        $select = [DB::raw('SUM(DATEDIFF(end_date,start_date)+1) as nb_days')];
        $tasks = $taskHelper->getBase($select);
        $tasks = $tasks->where('tasks.project_id', $id)
        					->groupBy(['task_types.id'])
        					->get();

        $result['project_tasks_summary'] = $tasks;

        return $result;
    }

    private function getDatePeriod($month, $year)
    {
        $dateFormat = "d/m/Y";

        try {
            $startDate = \DateTime::createFromFormat($dateFormat, "01/$month/$year");
            $endDate = \DateTime::createFromFormat($dateFormat, '' . $this->getMonthNbDays($month, $year) . "/$month/$year");

            return new DatePeriod(
                $startDate,
                new DateInterval('P1D'),
                $endDate
            );
        } catch (\Exception $e) {
            _helper('api')->error($e->getMessage());
        }
    }

    /**
     * Get the numbers of day in one month
     * @param $month
     * @param $year
     * @return int
     */
    private function getMonthNbDays($month, $year)
    {
        $shortMonth = [4, 6, 9, 11];
        $february = 2;

        if ($month == $february) {
            return $this->isLeapYear($year) ? 30 : 29;
        } elseif (in_array($month, $shortMonth)) {
            return 31;
        } else {
            return 32;
        }
    }

    private function isLeapYear($year)
    {
        return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
    }

    public function addFiles($id, $files, $message = false, $user = false, $contactId = false, $isShared = false)
    {
    	foreach($files as $file){
    		if(!empty($file['url'])){
    			$_file = new ProjectFile;
    			$_file->name = $file['name'];
    			$_file->path = storage_path($file['url']);
    			$_file->path = str_replace('//', '/', $_file->path);
    			$_file->extension = pathinfo(storage_path($file['url']), PATHINFO_EXTENSION);
    			$_file->uniqid = uniqid();
    			$_file->project_id = $id;
    			$_file->url_view = route('get_file', ['id' => $_file->uniqid]);
    			$_file->url_download = route('get_file', ['id' => $_file->uniqid, 'action' => 'download']);
          if ($user) {
            $_file->created_by = $user->id;
          } elseif ($isShared && $contactId && $contactId > 0) {
            $_file->created_by = $contactId;
          }
          if (!empty($message['id'])) {
            $_file->explorer_mission_conversation_message_id = $message['id'];
          }
    			$_file->save();

          $kolabProject = Project::findOrFail($id);
          if ($kolabProject) {
            $ownerId = $kolabProject->created_by;
            $owner = User::findOrFail($ownerId);
            if ($owner) {
              $mediaOwner = '';
              if ($user && $user->name && $owner->id != $user->id) {
                $mediaOwner = $user->name;
              } elseif ($contactId && $isShared) {
                $contact = DB::table('contacts')->where('id', $contactId)->first();
                $mediaOwner = $contact->email ?? '';
              }
              if ($mediaOwner != '') {
                $owner->notify(new AddMedia($kolabProject, $_file, $mediaOwner));
              }
            }
          }
    		}
    	}

    	// Output
    	$files = $this->getFiles($id);

    	return $files;
    }

    public function getFiles($id)
    {
    	// Output
    	$files = ProjectFile::where('project_id', $id)->get();

    	return $files;
    }

    public function deleteFiles($ids)
    {
    	foreach($ids as $id){
        $file = ProjectFile::find($id);
        try {
        	unlink(rawurldecode($file->path));
          $messageId = $file->explorer_mission_conversation_message_id;
          $message = ExplorerMissionConversationMessage::find($messageId);
          $message->delete();
          $file->delete();
        } catch (Exception $e) {
          _info($e->getMessage());
        }
      }

      return true;
    }

    public function deleteDrives($ids)
    {
      if (!empty($ids)) {
        foreach($ids as $id){
          $drive = ExplorerDriveLink::find($id);
          //$message = ExplorerMissionConversationMessage::where('id_drive_link', $id);
  
          try {
            return $drive->delete();
          } catch (Exception $e) {
            _info($e->getMessage());
          }
        }
      }

      return false;
    }

    // -- USEFUL

    /**
     * Find or create a new Client in the database and return the client ID
     * @param $params
     * @return mixed
     */
    private function findOrCreateClient($params)
    {
      if (!empty($params['project']['client_name']) || !empty($params['project']['client']['name'])) {
        $clientName = false;
        if (!empty($params['project']['client_name'])) {
          $clientName = $params['project']['client_name'];
        } elseif (!empty($params['project']['client']['name'])) {
          $clientName = $params['project']['client']['name'];
        }
        $client = Client::where('name', '=', $clientName)->first();

        if ($client == null) {
            $client = new Client();
            $client->name = $clientName;
            $client->created_by = (int)$params['user'][0];
            $client->updated_by = (int)$params['user'][0];

            try {
                $client->save();
            } catch (Exception $e) {
                _helper('api')->error($e->getMessage());
            }
        }

        return $client->id;
      }
    }

    /**
     * get talents from project task
     * @param $project
     * @return array
     */
    public function getTalentsFromTasks($project = [])
    {
        if (empty($project)) {
            return false;
        }
        $talents = [];
        $tasks = $project->getTaskAttribute();
        foreach ($tasks as $task) {
            if (!empty($task->getTalentsAttribute()[0])) {
                $talent = $task->getTalentsAttribute()[0];
                if (!isset($talents[$talent->id])) {
                  $talent->avatar = (!empty($talent->avatar)) ? $talent->avatar : false;
                  $talent->profilePicture = ($talent->avatar) ? '/upload/avatars/' . $talent->avatar : false;
                  $talent->initials = mb_substr($talent->firstname, 0, 1) . mb_substr($talent->lastname, 0, 1);
                    $talents[$talent->id] = $talent;
                }
            }
        }

        return $talents;
    }
   /**
     * get talents from project task
     * @param $project
     * @return string
     */
    public function getShareLink($project = [])
    {
        if (empty($project)) {
            return false;
        }
        if ($project->sharing_token) {
          if (route('share_project', $project->sharing_token)) {
            return route('share_project', $project->sharing_token);
          }
        }

        return false;
    }

    /**
     * get project step by task progression
     * @param $tasks
     * @return string
     */
    public function getProjectStep($tasks, $projectId)
    {
        $firstTask = Task::where('project_id', $projectId)->orderBy('start_date', 'asc')->first();
        $allTasksClosed = Task::where('project_id', $projectId)->where('status', '!=', 'CLOSED')->first();
        if (empty($tasks) || (!empty($firstTask) && $firstTask->start_date && new DateTime($firstTask->start_date) > new DateTime())) {
            return ProjectStep::WAITING;
        } elseif (empty($allTasksClosed)) {
            return ProjectStep::CLOSED;
        }

        return ProjectStep::IN_PROGRESS;
    }

    public function orderKanban($projects, $userId = false, $closed = false)
    {
      $projects = $projects->get();
      $kanban = ["WAITING" => [], "IN_PROGRESS" => [], "CLOSED" => []];
      $recentClosed = [];
      $now = Carbon::now();
      if ($projects->count() > 0) {
        foreach ($projects as $project) {
          if (($project && $project->categoryName && $project->categoryName != 'mission-explorer') || ($userId && $project && $project->categoryName == 'mission-explorer' && $project->clientId && $project->freelanceId && ($project->clientId == $userId || $project->freelanceId == $userId))) {
            $tasks = DB::table('tasks')->select('id')->where('project_id', $project->id)->get();
            $projectStep = $this->getProjectStep($tasks, $project->id);
            $project->formattedDateDeadline = Carbon::create($project->date_deadline)->translatedFormat('jS F');
            $project->deadlineDiffInDays = $now->diffInDays($project->date_deadline);
            $project->activeTaskType = $this->getActiveTaskType($project->id);
            $project->url = route('app_project_detail', $project->id);
            if ($projectStep) {
              switch($projectStep) {
                case 'WAITING':
                  $kanban["WAITING"][] = $project;
                  break;
                case 'IN_PROGRESS':
                  $kanban["IN_PROGRESS"][] = $project;
                  break;
                case 'CLOSED':
                  if (!$closed) {
                    $kanban["CLOSED"][] = $project;
                  }
                  break;
              }
            }
          }
        }
      } else {
        $project = Project::findOrFail(1);
        $kanban["IN_PROGRESS"][] = $project;
      }

      /*if ($closed) {
        foreach ($projects as $key => $closedProject) {
          if ($closedProject->projectStep == 'CLOSED') {
            $kanban["CLOSED"][] = $closedProject;
            $recentClosed[] = $closedProject->id;
          }
          if ($key == 2) {
            break;
          }
        }
        foreach ($closed->get() as $closedProject) {
          if ($closedProject->projectStep == 'CLOSED' && !in_array($closedProject->id, $recentClosed)) {
            $kanban["CLOSED"][] = $closedProject;
          }
        }
      }*/

      return $kanban;
    }

    public function getActiveTaskType($projectId) {
      $activeTaskType = [];
      $now = Carbon::now();
      $tasks = Task::where('project_id', $projectId)
        ->where('end_date', '>=', $now)
        ->where('status', 'PROGRESS')
        ->get();
      if (!empty($tasks)) {
        foreach ($tasks as $task) {
          if ($task->taskTypes->first()) {
            $taskType = $task->taskTypes->first()->name;
            if (!in_array($taskType, $activeTaskType)) {
              $activeTaskType[] = $taskType;
            }
          }
        }
      }

      return $activeTaskType;
    }

    public function createProjectConversation($params = null)
    {
        $projectId = $params['projectId'] ?? null;
        $project = Project::find($projectId);

        if (!empty($project)) {
            $projectMission = new ExplorerMission();
            $projectMission->name = $project->name;
            $projectMission->deadline = $project->date_deadline;
            $projectMission->type = 2; // project type by default / editing
            try {
                $projectMission->save();
            } catch (Exception $e) {
                _helper('api')->error($e->getMessage());
            }

            $projectMissionProposition = new ExplorerMissionProposition();
            $projectMissionProposition->client()->associate(\Auth::user());
            $projectMissionProposition->freelance()->associate(\Auth::user());
            $projectMissionProposition->explorerMission()->associate($projectMission);
            $projectMissionProposition->kolab_project_id = $project->id;
            try {
                $projectMissionProposition->save();
            } catch (Exception $e) {
                _helper('api')->error($e->getMessage());
            }

            $projectConversation = new ExplorerMissionConversation();
            $projectConversation->proposition()->associate($projectMissionProposition);
            try {
                $projectConversation->save();
            } catch (Exception $e) {
                _helper('api')->error($e->getMessage());
            }

            $project->proposition()->save($projectMissionProposition);

            $projectConversationDrive = new ExplorerDrive();
            $projectConversationDrive->missionProposition()->associate($projectMissionProposition);
            $projectConversationDrive->save();

            try {
                $projectConversationMessage->save();
            } catch (Exception $e) {
                _helper('api')->error($e->getMessage());
            }

            return ['response' => 'Project conversation created'];
        } else {
            return ['response' => 'Project not found', 'params' => $params];
        }
    }
}
