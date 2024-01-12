<?php

namespace App\Http\Helpers;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\Project;
use Carbon\CarbonPeriod;
use App\Models\TaskDates;
use App\Models\TaskType;
use Termwind\Components\Dd;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Events\TaskEvent;
use App\Events\UpdateProjectsEvent;
use App\Notifications\TaskEdit;
use App\Notifications\TaskAdd;

use SendinBlue\Client\Configuration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use App\Http\Helpers\NotificationHelper;
use Illuminate\Database\Eloquent\Builder;
use SendinBlue\Client\Model\SendSmtpEmail;
use Illuminate\Database\Eloquent\Collection;
use SendinBlue\Client\ApiException;
use Illuminate\Support\Facades\Log;

class TaskHelper
{

  private $taskUserChange = false;

  /**
   * Get all tasks
   * @param $request
   * @return Task[]|Collection [Object]  Task
   */
  public function all($request = [])
  {
    $tasks = Task::query();

    // Filter by name
    if (_helper('api')->checkParameterExistAndNotEmpty('filter_name', $request)) {
      $tasks->where('name', 'LIKE', '%' . $request['filter_name'] . '%');
    }

    // Filter by categories
    if (_helper('api')->checkParameterExistAndNotEmpty('filter_categories', $request)) {
      $tasks->whereIn('category_id', $request['filter_categories']);
    }

    return $tasks->orderBy('id', 'ASC')->get();
  }

  /**
   * Get task details
   * @param  [Integer] $id Task id
   * @return Task|Builder|Model|object|null [Object]  Task
   */
  public function get($id)
  {
    return Task::where('id', $id)->first();
  }

  /**
   * Get base
   */
  public function getBase($select = [])
  {
    $sTasks = [
      'tasks.id as id',
      'tasks.name as name',
      'tasks.note as note',
      'tasks.status as status',
      'tasks.accepted as accepted',
      'tasks.start_date as start_date',
      'tasks.end_date as end_date',
      'tasks.created_at as created_at',
    ];

    $sProjects = [
      'projects.id as project_id',
      'projects.name as project_name',
      'project_categories.name as project_category',
    ];

    $sTaskTypes = [
      'task_types.name as task_type',
    ];

    $sUsers = [
      'users.firstname as created_by',
      'users.avatar as profile'
    ];

    $sTaskTalent = [
      'task_talent.user_id as talent_id'
    ];

    $selected = array_merge($sTasks, $sProjects, $sTaskTypes, $sUsers, $sTaskTalent, $select);

    $tasks = Task::query()->select($selected)
      ->join('task_talent', 'tasks.id', '=', 'task_talent.task_id')
      ->join('projects', 'tasks.project_id', '=', 'projects.id')
      ->join('project_categories', 'projects.category_id', 'project_categories.id')
      ->join('task_type_task', 'tasks.id', '=', 'task_type_task.task_id')
      ->join('task_types', 'task_type_task.task_type_id', '=', 'task_types.id')
      ->join('users', 'tasks.created_by', '=', 'users.id')
      //->join('users', 'tasks.created_by', '=', 'users.avatar')
      ->with('talents')
      ->with('tasks');

    return $tasks;
  }

  /**
   * Delete One Task
   *
   * @param $id
   * @return bool
   * @throws Exception
   */
  public function delete($id)
  {
    $task = TaskDates::find($id);
    $parentTaskId = $task->task_id;
    $parentTask = Task::find($parentTaskId);

    try {
      $task->delete();
      if (count($parentTask->tasks) == 0) {
        $parentTask->delete();
      }
    } catch (Exception $e) {
      _helper('api')->error($e->getMessage());
    }

    return true;
  }

  /**
   * Delete Parent Task & child tasks
   *
   * @param $id
   * @return bool
   * @throws Exception
   */
  public function deleteTask($id)
  {
    $parentTask = Task::find($id);
    $taskHistory = DB::table('tasks_historic')->where('task_id', $id);
    try {
      $taskHistory->delete();
      foreach ($parentTask->tasks as $task) {
        $task->delete();
      }
      $parentTask->delete();
    } catch (Exception $e) {
      _helper('api')->error($e->getMessage());
    }

    return true;
  }


  /**
   * Add or update a task
   * @param
   * @return mixed [Object]  Task
   */
  public function addOrUpdate($params)
  {
      $savedTask = [];
      $projectId = $params['project'] ?? false;
      $edit = $params['edit'] ?? false;
      $formType = $params['formType'] ?? false;
  
      $newTaskListId = 11; 
      $editTaskListId = 12; 
      $identifier = false;
      if (!empty($params['tasks'])) {
          foreach ($params['tasks'] as $taskParams) {
              $savedTask[] = $this->saveTask($projectId, $taskParams, (int)$params['user'], $edit);
              $talent_id = $taskParams['talent_id'] ?? false;
              /*if ($talent_id) {
                  $createdByName = '';
                  $nameto = null;
                  $project = Project::findOrFail($projectId);
                  $projectName = $project->name;
                  $user = \App\User::findOrFail($talent_id);
                  $email = false;
                  $taskType = TaskType::findOrFail($taskParams['task_type_id']);
                  $taskName = '';
                  if ($taskType) {
                    $taskName = __('skills.' . $taskType->name);
                  }
                  $url_project = url('/project/' . $projectId);
                  if (!empty($user)) {
                      $email = $user->email;
                      $nameto = $user->firstname;
                  }
           
  
                  $task_user = \App\User::findOrFail($taskParams['createdBy']);
                  if (!empty($task_user)) {
                      $createdByName = $task_user->firstname;
                  }
                  $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', (env('SENDINBLUE_KEY')));
                  $apiContact = new \SendinBlue\Client\Api\ContactsApi(new \GuzzleHttp\Client(), $config);
                  
                  $updateContact = new \SendinBlue\Client\Model\UpdateContact();
                  $createContact = new \SendinBlue\Client\Model\CreateContact();
                  
                  $createContact['email'] = $email;
                  $createContact['attributes'] = [
                      'NAMETO' => $nameto,
                      'NAMEFROM' => $createdByName,
                      'URL_PROJECT' => $url_project,
                      'PROJECT_NAME' => $projectName,
                      'TASK_NAME' => $taskName,
                      'PROJECT_ID' => $projectId,
                  ];
                  if ($edit) {
                    $createContact['listIds'] = [$editTaskListId];
                    $updateContact['listIds'] = [$editTaskListId];
                  } else {
                    $createContact['listIds'] = [$newTaskListId];
                    $updateContact['listIds'] = [$newTaskListId];
                  }
                  
                  $createContact['updateEnabled'] = true;
                  $limit = 50;
                  $offset = 0;
                  $exist = false;
                  $contacts = $apiContact->getContacts($limit, $offset);
                  $contacts = $contacts["contacts"] ?? false;
                  if ($contacts) {
                      foreach($contacts as $is_contact){
                          if (!empty($is_contact["email"]) && $is_contact["email"] == $email) {
                              $exist = true;
                          }
                      }
                  }
                  try {
                    if (!$exist) {
                      $apiContact->createContact($createContact);
                    } else {
                      $identifier = $email;
                      $apiContact->updateContact($identifier, $updateContact);
                      $apiContact->deleteContact($identifier);
                    }
                  } catch (Exception $e) {
                    _helper('api')->error($e->getMessage());
                  }
              }*/
    
      }

      if ($formType && $formType == 'projects') {
        event(new UpdateProjectsEvent());
      }

      event(new TaskEvent());
    }

    return $savedTask;
  }



  /**
   * @param $request
   */
  public function put($id, $userId)
  {
    return DB::table('task_viewed')->where('task_id', $id)->where('user_id', $userId)->delete();
  }

  /**
   * @param $request
   */
  public function updateDate($id, $request)
  {
    $startDate = ($request->start_date) ? Carbon::parse($request->start_date) : false;
    $endDate = ($request->end_date) ? Carbon::parse($request->end_date) : false;
    $fromDate = ($request->from_date) ? Carbon::parse($request->from_date) : false;
    $toStartDate = ($request->to_date) ? Carbon::parse($request->to_date) : false;
    $toEndDate = ($request->to_date) ? Carbon::parse($request->to_date) : false;
    $talentId = $request->talentId ?? false;

    $period = CarbonPeriod::create($startDate, $endDate);
    $diffDays = $startDate->diffInDays($endDate);
    $dates = $period->toArray();
    $lastKey = count($dates) - 1;
    $keyDate = array_search($fromDate, $dates);
    if ($keyDate !== false && $toStartDate && $toEndDate) {
      if ($keyDate != 0 || $keyDate != $lastKey) {
        $beginningDiff = $startDate->diffInDays($fromDate);
        $endingDiff = $fromDate->diffInDays($endDate);
        $startDate = ($beginningDiff == 1) ? $toStartDate->subDay() : $toStartDate->subDays($beginningDiff);
        $endDate = ($endingDiff == 1) ? $toEndDate->addDay() : $toEndDate->addDays($endingDiff);
      } else if ($keyDate == 0) {
        $startDate = $toStartDate;
        $endDate = $toStartDate->addDays($diffDays);
      } else if ($keyDate == $lastKey) {
        $endDate = $toStartDate;
        $startDate = $toEndDate->subDays($diffDays);
      }

      if ($startDate && $endDate && $id) {
        $task = Task::find($id);
        $task->start_date = $startDate;
        $task->end_date = $endDate;

        try {
          $task->save();
        } catch (Exception $e) {
          _helper('api')->error($e->getMessage());
        }
      }
      $currentTaskTalentId = $task->talents()->first()->id ?? false;

      if ($talentId) {
        $task->talents()->detach();
        $task->talents()->sync([$talentId]);
        try {
          if ($talentId && $talentId != $currentTaskTalentId) {
            DB::table('tasks_historic')->insert(['from_user_id' => $currentTaskTalentId, 'to_user_id' => $talentId, 'task_id' => $task->id]);
          }
          $task->save();
        } catch (Exception $e) {
          _helper('api')->error($e->getMessage());
        }
      }

      return true;
    }

    return false;
    /* $startDate = ($request->start_date) ? Carbon::parse($request->start_date) : false;
    $endDate = ($request->end_date) ? Carbon::parse($request->end_date) : false;
    if ($id) {
      $task = Task::find($id);
      if ($startDate && $endDate) {
        $task = DB::table('task_dates')->where('id', '=', $id)->update(['start_date' => $startDate, 'end_date' => $endDate]);
      }
    } // TO USE LATER */
  }

  /**
   * @param $request
   */
  public function putNote($id, $request)
  {
    $note = $request->params;
    $task = Task::find($id);
    $task = DB::table('tasks')->where('id', '=', $id)->update(['note' => $note]);
  }

  /**
   * Patch a task status
   *
   * @param $id
   * @param $params
   */
  public function patch($id, $params)
  {
    $this->helpers['notification'] = new NotificationHelper;
    $task = Task::find($id);

    //If task not found by ID return an error
    if ($task == null) {
      _helper('api')->error('Task ID invalid');
    }


    if ($params['action']) {
      switch ($params['action']) {
        case 'set_acceptation':
          $task->accepted = $params['value'];
          break;
        case 'set_closed':
          $task->status = 'CLOSED';
          $task->closed_at = new \DateTime();
          if ($task && $task->project_id) {
            $projectId = $task->project_id;
            $project = Project::findOrFail($projectId);
            if ($project) {
              $project->updated_at = new \DateTime();
              $project->save();
            }
          }

          $this->helpers['notification']->save("La tâche " . $task->taskTypes[0]->name . " du projet " . $task->project->name . " a bien été clôturé", null, 'TÂCHE', $task->id);
          break;
      }
    }
    
    $this->setNotificationForClosedTask($task);

    // Catch error during saving
    try {
      $task->save();
      event(new TaskEvent());

      return $task;
    } catch (Exception $e) {
      _helper('api')->error('An error occurred during save  : ' . $e->getMessage());
    }
  }

  private function setNotificationForClosedTask($task)
  {
    $viewedDatas = [];
    $taskCreatorId = $task->created_by ?? false;
    $taskUserId = $task->talentId[0]->user_id ?? false;
    $taskViewedByCreator = DB::table('task_viewed')->where('task_id', $task->id)->where('user_id', $taskCreatorId)->get();
    if ($taskViewedByCreator->isEmpty()) { $viewedDatas[] = ['userId' => $taskCreatorId, 'taskId' => $task->id]; }
    $taskViewedByUser = DB::table('task_viewed')->where('task_id', $task->id)->where('user_id', $taskUserId)->get();
    if ($taskViewedByUser->isEmpty()) { $viewedDatas[] = ['userId' => $taskUserId, 'taskId' => $task->id]; }

    if (!empty($viewedDatas)) {
      foreach ($viewedDatas as $data) {
        DB::table('task_viewed')->insert(
          ['user_id' => $data['userId'], 'task_id' => $data['taskId']]
        );
      }
    }
  }

  // -- Useful

  private function saveTask($project, $taskParams, $user = null, $edit = false)
  {
    $this->helpers['notification'] = new NotificationHelper;
    $mandatoryParameters = [];
    // Check if all parameters are provided
    try {
      _helper('api')->checkParameters($taskParams, $mandatoryParameters);
    } catch (Exception $e) {
      // Return the exception message if error
      _helper('api')->error($e->getMessage());
    }
    $taskDate = false;
    if ($edit && !empty($taskParams['task_id'])) {
      $action = 'UPDATE';
      //$taskDate = TaskDates::find($taskParams['id']);
      $task = Task::find($taskParams['task_id']);
    } else {
      if (!empty($taskParams['id']) && $taskParams['id']) {
        $action = 'UPDATE';
        $task = Task::find($taskParams['id']);
        $task->updated_by = $user;
      } else {
        $action = 'ADD';
        $task = new Task();
        $task->created_by = $taskParams['createdBy'];
        $task->updated_by = $taskParams['createdBy'];
        $task->status = 'PROGRESS';
      }
    }

    $taskTalentId = $taskParams['talent_id'] ?? false;
    if ($taskTalentId && $project) {
      $talent = User::findOrFail($taskTalentId);
      $taskProject = Project::findOrFail($project);
      if ($talent && $taskProject) {
        $namefrom = "";
        $nameto = $talent->firstname ?? "";
        $taskCreatorId = $taskParams['createdBy'] ?? false;
        $taskCreator = User::findOrFail($taskCreatorId);
        if ($taskCreator) {
          $namefrom = $taskCreator->firstname;
        }
        $urlProject = $taskProject->url;
        $taskTypeId = $taskParams['task_type_id'] ?? false;
        $taskType = TaskType::findOrFail($taskTypeId);
        $taskName = '';
        if ($taskType) {
          $taskName = __('skills.' . $taskType->name);
        }
        $projectName = $taskProject->name ?? "";
      }
    }

    if ($action == 'UPDATE') {
      if ($this->isTaskEdited($task, $taskParams, $user) && $talent && $namefrom && $nameto && $urlProject && $taskName && $projectName && $project) {
            $task->updated_at = new \DateTime();
            //if (!$this->taskUserChange) {
              $userAssignedId = $task->talentId[0]->user_id ?? false;
              $userAssigned = User::findOrFail($userAssignedId);
              $userAssigned->notify(new TaskEdit($namefrom, $nameto, $urlProject, $taskName, $projectName, $project));
            //}
            $this->taskUserChange = false;
      }
    }

    if ($action == 'ADD' && $taskTalentId) {
        $talent = User::findOrFail($taskTalentId);
        $talent->notify(new TaskAdd($namefrom, $nameto, $urlProject, $taskName, $projectName, $project));
    }

    $task->name = !empty($taskParams['name']) ? $taskParams['name'] : false;
    if ($action == 'ADD') {
      $task->project_id = $project;
    }
    $task->note = !empty($taskParams['note']) ? $taskParams['note'] : "[]";
    $taskStartDate = false;
    $taskEndDate = false;
    if (!empty($taskParams['date']['start'])) {
      /*if ($edit) {
        $taskDate->start_date = Carbon::parse($taskParams['date']['start']);
        $taskDate->end_date = Carbon::parse($taskParams['date']['end']);
        $taskDate->note = !empty($taskParams['note']) ? $taskParams['note'] : null;
      } else {*/
      $task->start_date = Carbon::parse($taskParams['date']['start']);
      $task->end_date = Carbon::parse($taskParams['date']['end']);
      //}
      $taskStartDate = $taskParams['date']['start'];
      $taskEndDate = $taskParams['date']['end'];
    } else if (!empty($taskParams['start_date']) && !empty($taskParams['end_date'])) {
      /*if ($edit) {
        $taskDate->start_date = Carbon::parse($taskParams['start_date']);
        $taskDate->end_date = Carbon::parse($taskParams['end_date']);
        $taskDate->note = !empty($taskParams['note']) ? $taskParams['note'] : null;
      } else {*/
      $task->start_date = Carbon::parse($taskParams['start_date']);
      $task->end_date = Carbon::parse($taskParams['end_date']);
      //}
      $taskStartDate = $taskParams['start_date'];
      $taskEndDate = $taskParams['end_date'];
    }
    $task->end_date = $task->end_date->hour(20);
    try {
      $task->save();
  
      /*if ($taskDate) {
        $taskDate->save();
      }*/
    } catch (Exception $e) {
      _helper('api')->error($e->getMessage());
    }
    /*if (!$edit) {
      $taskStartDate = Carbon::parse($taskStartDate);
      $taskEndDate = Carbon::parse($taskEndDate);
      $diffDays = $taskStartDate->diffInDays($taskEndDate);
      if ($taskStartDate !== $taskEndDate) {
        $diffDays = $diffDays + 1;
      }
      $period = CarbonPeriod::create($taskStartDate, $diffDays);
      $dates = $period->toArray();
      foreach ($period as $date) {
        $taskDate = new TaskDates();
        $taskDate->task_id = $task->id;
        $taskDate->project_id = $project;
        $taskDate->start_date = Carbon::parse($date);
        $taskDate->end_date = Carbon::parse($date);
        $taskDate->note = !empty($taskParams['note']) ? $taskParams['note'] : "[]";
        $taskDate->save();
      }
    }*/

    if (!empty($taskParams['talent_id']) /*&& is_array($taskParams['talent_id'])*/) {
      $task->talents()->detach();
      $task->talents()->sync([$taskParams['talent_id']]);
      // If assigned user is the creator, accept task automatically
      if ($taskParams['talent_id'] == $task->created_by) {
        $task->accepted = 1;
      }
      // If assigned user didn't create the task, accept task automatically
      if ($taskParams['talent_id'] !== $task->created_by) {
        $task->accepted = 1;
      }
    }

    if (!empty($taskParams['task_type_id']) /*&& is_array($taskParams['task_type_id'])*/) {
      $task->taskTypes()->detach();
      $task->taskTypes()->sync([$taskParams['task_type_id']]);
    }

    try {
      $task->save();
      if (!empty($taskParams['createdBy']) && !empty($taskParams['talent_id'])) {
        $taskDatas = [
          ['userId' => $taskParams['createdBy'], 'taskId' => $task->id],
          ['userId' => $taskParams['talent_id'], 'taskId' => $task->id]
        ];

        foreach ($taskDatas as $data) {
          DB::table('task_viewed')->insert(
            ['user_id' => $data['userId'], 'task_id' => $data['taskId']]
          );
        }
      }

      $icon = $action == 'ADD' ? '🆕' : '✅';
      $action = $action == 'ADD' ? 'créé' : 'modifié';

      $this->helpers['notification']->save("$icon La tâche " . $task->taskTypes[0]->name . " du projet " . $task->project->name . " a bien été " . $action . "", $user, 'TÂCHE', $task->id);
    } catch (Exception $e) {
      _helper('api')->error($e->getMessage());
    }

    return $task;
  }

  private function isTaskEdited($task, $params, $user = false) {
      //userSelected
      $taskId = $task->talentId[0]->user_id ?? false;
      $paramTaskId = $params['talent_id'] ?? false;
      if ($taskId != $paramTaskId && $user) {
        $taskTypeId = $params['task_type_id'] ?? false;
        $projectId = $task->project_id ?? false;
        $user = User::findOrFail($user);
        if ($taskTypeId && $projectId && $user) {
          $talent = User::findOrFail($paramTaskId);
          $project = Project::findOrFail($projectId);
          $taskType = TaskType::findOrFail($taskTypeId);
          $nameto = $talent->name;
          $namefrom = $user->name;
          $urlProject = $project->url;
          $projectName = $project->name;
          $taskName = $taskType->name;
          $talent->notify(new TaskAdd($namefrom, $nameto, $urlProject, $taskName, $projectName, $projectId));
          $this->taskUserChange = true;
        }

        return true;
      }
      //tasktypeId 
      $taskTypeId = $task->taskTypes[0]->id ?? false;
      $paramTaskTypeId = $params['task_type_id'] ?? false;
      if ($taskTypeId != $paramTaskTypeId) {
        return true;
      }
      //date
      $taskStartDate = Carbon::parse($task->start_date)->format('Y-m-d') ?? false;
      $paramTaskStartDate = Carbon::parse($params['date']['start'])->format('Y-m-d') ?? false;
      if ($taskStartDate != $paramTaskStartDate) {
        return true;
      }
      $taskEndDate = Carbon::parse($task->end_date)->format('Y-m-d') ?? false;
      $paramTaskEndDate =  Carbon::parse($params['date']['end'])->format('Y-m-d') ?? false;
      if ($taskEndDate != $paramTaskEndDate) {
        return true;
      }
      //note
      $taskNote = $task->note ?? false;
      $paramTaskNote = $params['note'] ?? json_encode([]);
      if ($taskNote != $paramTaskNote) {
        return true;
      }

      return false;
    }

  public function closeTask()
  {
    $tasks = $this->tasksToClose();

    if (!empty($tasks)) {
      foreach ($tasks as $task) {
        $taskId = $task->id;
        $task = Task::findOrFail($taskId);
        if ($task) {
          $task->status = 'CLOSED';
          $task->closed_at = new \DateTime();
        }

        try {
          $task->save();
          $this->setNotificationForClosedTask($task);
        } catch (Exception $e) {
          _helper('api')->error($e->getMessage());
        }
      }
    }
    event(new TaskEvent());
  }

  public function tasksToClose($userId = false)
  {
    $now = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());
    $startDay = Carbon::create($now->format('Y'), $now->format('m'), $now->format('d'), 00, 00, 00);
    $endDay = Carbon::create($now->format('Y'), $now->format('m'), $now->format('d'), 23, 59, 59);
    $tasks = DB::table('tasks')
    ->select('tasks.id', 'end_date')
    ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
    ->leftJoin('task_talent', 'tasks.id', '=', 'task_talent.task_id')
    ->whereNot('projects.category_id', 9);
    if ($userId) {
      $tasks = $tasks->where('task_talent.user_id', $userId);
      $user = User::findOrFail($userId);
      $workspace = $user->currentWorkspace;
      if ($workspace) {
        $tasks->where('projects.workspace_id', $workspace);
      }
    }
    $tasks = $tasks->where('tasks.status', 'PROGRESS')->whereBetween('tasks.end_date', [$startDay, $endDay])->orderBy('tasks.closed_at', 'desc')->get();

    if ($tasks) {
      return $tasks;
    }

    return [];
  }
}