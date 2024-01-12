<?php

namespace App\Http\Helpers;


use App\Models\Appointment;
use App\Models\Task;
use App\Models\TaskDates;
use App\Models\TaskType;
use App\Models\Group;
use App\Models\Project;
use App\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlanningHelper
{
  private $month;
  private $year;
  private $monthes;

  public function __construct() {
    $this->month = false;
    $this->year = false;
    $this->monthes = ['1' => 'january', '2' => 'february', '3' => 'march', '4' => 'april', '5' => 'may', '6' => 'june', '7' => 'july', '8' => 'august', '9' => 'september', '10' => 'october', '11' => 'november', '12' => 'december'];
  }

  public function get($params)
  {
    // if (!_helper('api')->checkParameterExistAndNotEmpty('filter_talents_id', $params)) {
    //     $params['filter_talents_id'] = [2]; // Todo : If no talent selected return the planning for the current user only
    // }
    if (!_helper('api')->checkParameterExistAndNotEmpty('date_month', $params) || !_helper('api')->checkParameterExistAndNotEmpty('date_year', $params)) {
      $params['date_month'] = date('m');
      $params['date_year'] = date('Y');
    }

    $period = $this->getDatePeriod($params['date_month'], $params['date_year']);

    $tasksByDate = $this->getTasksByDate(
      $params['filter_talents_id'],
      $params['date_month'],
      $params['date_year'],
      $params
    );

    $appointmentsByDate = $this->getAppointmentsByDate(
      $params['filter_talents_id'],
      $params['date_month'],
      $params['date_year']
    );

    setlocale(LC_TIME, 'fr_FR');
    date_default_timezone_set('Europe/Paris');
    $dateFormat = 'Y-m-d';

    // Populating the Planning Array
    foreach ($period as $date) {
      //$dateFr = mb_convert_encoding(ucWords(strftime('%A %d %h %Y', strtotime($date->format('Y-m-d')))), 'UTF-8');
      $_date = $date->format('Y-m-d');
      $date->setTime(00, 00, 00);

      // Initializing empty array for the date in case there is no task at the date
      $result['planning'][$_date] = [];
      foreach ($tasksByDate as $talentId => $task) {
        //if (($date >= DateTime::createFromFormat($dateFormat, $task->start_date)) && ($date <= DateTime::createFromFormat($dateFormat, $task->end_date))) {
        // Add the task if the current date is in the task interval
        //dd($tasksByTalent);
        //foreach ($tasksByTalent as $talentId => $task) {
        //foreach ($tasks as $task) {
        $start_date = DateTime::createFromFormat('Y-m-d H:i:s', $task->start_date)->setTime(00, 00, 00);
        $end_date = DateTime::createFromFormat('Y-m-d H:i:s', $task->end_date)->setTime(00, 00, 00);
        if (($date >= $start_date) && ($date <= $end_date)) {
          if ($task->project_category != "mission-explorer") {
            $task->type = "TASK";
          } elseif ($task->project_category == "mission-explorer") {
            $task->type = "MISSION EXPLORER";
          }
          $closedAt = $task->closed_at;
          $closedAt = new DateTime($closedAt);
          $closedAt = $closedAt->format('d F');
          $task->closedAt = $closedAt;
          $task->taskTypes = [(object)['id' => $task->task_type_id, 'name' => $task->task_type_id]];
          $result['planning'][$_date][$talentId]['events'][] = $task;
        }
        //}
        //}
        //}
      }

      $date = $date->format('Y-m-d');
      foreach ($appointmentsByDate as $key => $appointmentsByTalent) {
        if ($date == $key) {
          // Add the task if the current date is in the task interval
          foreach ($appointmentsByTalent as $talentId => $appointments) {
            foreach ($appointments as $appointment) {
              $appointment->type = "APPOINTMENT";
              $result['planning'][$_date][$talentId]['events'][] = $appointment;
            }
          }
        }
      }
    }

    // Retrieve the talents names for the planning head
    $result['talent_name'] = $this->getTalentsName($params['filter_talents_id']);

    $result['task_name'] = [];
    if (!empty($params['filter_task_types_id']))
      $result['task_name'] = $this->getTasksName($params['filter_task_types_id']);

    return $result;
  }

  public function getPlanning($params)
  {
    $result = [];

    if (!_helper('api')->checkParameterExistAndNotEmpty('date_month', $params) || !_helper('api')->checkParameterExistAndNotEmpty('date_year', $params)) {
      $params['date_month'] = date('m');
      $params['date_year'] = date('Y');
    }

    $period = $this->getDatePeriod($params['date_month'], $params['date_year']);

    $tasksByDate = $this->getPlanningTasksByDate(
      $params['filter_talents_id'],
      $params['date_month'],
      $params['date_year'],
      $params
    );

    $tasksByDate = $tasksByDate->groupBy('talent_id');
    $appointmentsByDate = $this->getAppointmentsByDate(
      $params['filter_talents_id'],
      $params['date_month'],
      $params['date_year']
    );

    setlocale(LC_TIME, 'fr_FR.UTF-8');
    $dateFormat = 'Y-m-d';


    foreach ($period as $date) {
      //$dateFr = mb_convert_encoding(ucWords(strftime('%A %d %h %Y', strtotime($date->format('Y-m-d')))), 'UTF-8');
      $_date = $date->format('Y-m-d');
      $date->setTime(00, 00, 00);

      // Initializing empty array for the date in case there is no task at the date
      $result['planning'][$_date] = [];
      foreach ($tasksByDate as $talentId => $tasks) {
        foreach($tasks as $task) {  
          $start_date = DateTime::createFromFormat('Y-m-d H:i:s', $task->start_date)->setTime(00, 00, 00);
          $end_date = DateTime::createFromFormat('Y-m-d H:i:s', $task->end_date)->setTime(00, 00, 00);
          if (($date >= $start_date) && ($date <= $end_date)) {
            if ($task->project_category != "mission-explorer") {
              $task->type = "TASK";
            } elseif ($task->project_category == "mission-explorer") {
              $task->type = "MISSION EXPLORER";
            }
            if (!empty($task->taskTypes[0])) {
              $task->taskTypeId = $task->taskTypes[0]->id;
            }
            $projectId = $task->project_id;
            /*$project = Project::findOrFail($projectId);
            if ($project) {
              $task->project = $project;
            }*/
            $task->taskTypes = [(object)['id' => $task->task_type_id, 'name' => $task->task_type_id]];
            $task->formattedStartDate = Carbon::create($task->start_date)->translatedFormat('jS F');
            $task->formattedEndDate = Carbon::create($task->end_date)->translatedFormat('jS F');
            
            $result['planning'][$_date][$talentId]['events'][] = $task;
          }
        }
        //if (($date >= DateTime::createFromFormat($dateFormat, $key)) && ($date <= DateTime::createFromFormat($dateFormat, $key))) {
        // Add the task if the current date is in the task interval
        //dd($tasksByTalent);
        //foreach ($tasksByTalent as $talentId => $task) {
        //foreach ($tasks as $task) {

        //}
        //}
        //}
      }

      $date = $date->format('Y-m-d');
      foreach ($appointmentsByDate as $key => $appointmentsByTalent) {
        if ($date == $key) {
          // Add the task if the current date is in the task interval
          foreach ($appointmentsByTalent as $talentId => $appointments) {
            foreach ($appointments as $appointment) {
              $appointment->type = "APPOINTMENT";
              $result['planning'][$_date][$talentId]['events'][] = $appointment;
            }
          }
        }
      }
    }
    // Retrieve the talents names for the planning head
    $result['talent_name'] = $this->getTalentsName($params['filter_talents_id']);

    $result['task_name'] = [];
    if (!empty($params['filter_task_types_id'])) {
      $result['task_name'] = $this->getTasksName($params['filter_task_types_id']);
    }

    return $result;
  }

  //
  // -- Useful
  //

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

  private function isLeapYear($year)
  {
    return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
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

  /**
   * Return the tasks grouped by dates and talent id
   *
   * @param $selectedTalentsId
   * @param int $month
   * @param int $year
   * @param $params
   * @return Task[]|Collection|Builder[]|\Illuminate\Support\Collection
   *
   */
  private function getTasksByDate($selectedTalentsId, int $month, int $year, $params)
  {
    $workspaceId = $params['workspace'] ?? 1;
    $userId = false;
    if (count($selectedTalentsId) == 1) {
      $userId = $selectedTalentsId[0] ?? false;
    }
    $tasksByDate = User::query()
      ->select([
        'tasks.id as id',
        'tasks.name as name',
        'tasks.note as note',
        'tasks.status as status',
        'tasks.accepted as accepted',
        'projects.id as project_id',
        'projects.name as project_name',
        'project_categories.name as project_category',
        'task_types.name as task_type',
        'tasks.start_date as start_date',
        'tasks.end_date as end_date',
        'tasks.created_at as created_at',
        'users.firstname as created_by',
        'users.id as talent_id',
        DB::raw('DATE(tasks.start_date) as task_date')
      ])
      ->join('task_talent', 'users.id', '=', 'task_talent.user_id')
      ->join('tasks', 'task_talent.task_id', '=', 'tasks.id')
      //->join('task_dates', 'task_dates.task_id', '=', 'tasks.id')
      ->join('projects', 'tasks.project_id', '=', 'projects.id')
      ->join('project_categories', 'projects.category_id', 'project_categories.id')
      ->join('task_type_task', 'tasks.id', '=', 'task_type_task.task_id')
      ->join('task_types', 'task_type_task.task_type_id', '=', 'task_types.id')
      ->leftJoin('workspaces', 'projects.workspace_id', '=', 'workspaces.id')
      ->whereIn('task_talent.user_id', $selectedTalentsId)
      ->where('tasks.accepted', 1)
      ->where('workspaces.id', '=', $workspaceId)
      ->where('tasks.status', '=', 'PROGRESS');
      if ($userId) {
        $tasksByDate = $tasksByDate->orWhere(function ($query) use ($userId) {
          $query->where('projects.category_id', 9)->where('tasks.created_by', '=', $userId);
        });
      }

    if (_helper('api')->checkParameterExistAndNotEmpty("filter_projects_id", $params)) {
      $tasksByDate->whereIn('tasks.project_id', [$params['filter_projects_id']]);
    }

    if (_helper('api')->checkParameterExistAndNotEmpty("filter_task_types_id", $params)) {
      $tasksByDate->whereIn('task_type_task.task_type_id', $params['filter_task_types_id']);
    }
    return $tasksByDate->groupBy(['talent_id', 'task_date', 'task_type'])->get();
  }

  /**
   * Return the tasks grouped by dates and talent id
   *
   * @param $selectedTalentsId
   * @param int $month
   * @param int $year
   * @param $params
   * @return Task[]|Collection|Builder[]|\Illuminate\Support\Collection
   *
   */
  private function getPlanningTasksByDate($selectedTalentsId, int $month, int $year, $params)
  {
    $workspaceId = $params['workspace'] ?? 1;

    $this->month = $month;
    $this->year = $year;
    $tasksByDate = User::query()
      ->select(['*', 
        'tasks.id as id',
        'tasks.name as name',
        'tasks.note as note',
        'tasks.status as status',
        'tasks.accepted as accepted',
        'projects.id as project_id',
        'projects.name as project_name',
        'project_categories.name as project_category',
        'task_types.name as task_type',
        'task_types.id as task_type_id',
        'tasks.start_date as start_date',
        'tasks.end_date as end_date',
        'tasks.created_at as created_at',
        'tasks.created_by as taskCreatorId',
        'users.firstname as created_by',
        'users.id as talent_id',
        'tasks.closed_at as closed_at',
        DB::raw('DATE(tasks.start_date) as task_date')
      ])
      ->join('task_talent', 'users.id', '=', 'task_talent.user_id')
      ->join('tasks', 'task_talent.task_id', '=', 'tasks.id')
      //->join('task_dates', 'task_dates.task_id', '=', 'tasks.id')
      ->join('projects', 'tasks.project_id', '=', 'projects.id')
      ->join('project_categories', 'projects.category_id', 'project_categories.id')
      ->join('task_type_task', 'tasks.id', '=', 'task_type_task.task_id')
      ->join('task_types', 'task_type_task.task_type_id', '=', 'task_types.id')
      ->leftJoin('workspaces', 'projects.workspace_id', '=', 'workspaces.id')
      ->with('tasks')
      ->whereIn('task_talent.user_id', $selectedTalentsId)
      ->where('tasks.accepted', 1)
      ->where('workspaces.id', '=', $workspaceId)
      ->where(function ($query) {
        $carbon = Carbon::now();
        $carbon->month = $this->month;
        $carbon->year = $this->year;
        $firstDay = $carbon->startOfMonth()->format('Y-m-d H:i:s');
        $lastDay = $carbon->endOfMonth()->format('Y-m-d H:i:s');
        $query->whereBetween('tasks.start_date', [$firstDay, $lastDay]);
        $query->OrWhereBetween('tasks.end_date', [$firstDay, $lastDay]);
      });
    return $tasksByDate->groupBy(['talent_id', 'task_date', 'task_type'])->get();
  }

  /**
   * Return the appointments grouped by dates and talent id
   *
   * @param $selectedTalentsId
   * @param int $month
   * @param int $year
   * @return Appointment[]|Collection|Builder[]|\Illuminate\Support\Collection
   */
  private function getAppointmentsByDate($selectedTalentsId, int $month, int $year)
  {
      $appointments = DB::table('appointment_talent')
      ->select(['*', DB::raw('DATE(datetime) as appointment_date'), 'appointments.user_id as talent_id', 'appointment_talent.user_id as atuid'])
      ->join('appointments', 'appointment_talent.appointment_id', '=', 'appointments.id')
      ->whereIn('appointments.user_id', $selectedTalentsId)
      ->whereMonth('datetime', $month)
      ->whereYear('datetime', $year)
      ->get()
      ->groupBy(['appointment_date', 'atuid']);
    return $appointments;
  }

  /**
   * Return the appointments grouped by dates and talent id
   *
   * @param $selectedTalentsId
   * @return Appointment[]|Collection|Builder[]|\Illuminate\Support\Collection
   */
  private function getTalentsName($selectedTalentsId)
  {
    $talents = User::select(['id', 'lastname', 'firstname'])
      ->whereIn('id', $selectedTalentsId)
      ->get();

    return $talents->mapToGroups(function ($item, $key) {
      return [$item['id'] => $item];
    });
  }

  private function getTasksName($selectedTasksId)
  {
    $tasks = TaskType::select(['id', 'name'])
      ->whereIn('id', $selectedTasksId)
      ->get();

    return $tasks->mapToGroups(function ($item, $key) {
      return [$item['id'] => $item];
    });
  }

  public function addOrUpdateGroup($params)
    {
      $groupParam = $params['group'];

      if (isset($groupParam['id']) && $groupParam['id']) {
      	$action = 'UPDATE';
        $group = Group::find($groupParam['id']);
      } else {
      	$action = 'ADD';
        $group = new Group();
      }
      $group->name = $groupParam['name'] ?? '';
      $group->color = $groupParam['color'] ?? '';
      if ($action == 'ADD') {
        $group->workspace_id = $groupParam['workspace_id'] ?? '';
      }
      $talents = $groupParam['talents'] ?? false;

      if ($talents) {
        foreach ($talents as $key => $talent) {
          if (!empty($talent['id'])) {
            $talents[$key] = $talent['id'];
          }
        }
      }

      if ($group->talents) {
        foreach ($group->talents as $key => $talent) {
          if (!empty($talent['id'])) {
            $group->talents[$key] = $talent['id'];
          }
        }
      }
      try {
        $group->save();

      } catch (Exception $e) {
        _helper('api')->error($e->getMessage());
      }

      $group->talents()->detach();
      if (isset($talents) && is_array($talents)) {
        $group->talents()->sync($talents);
      }

        try {
        $group->save();
      } catch (Exception $e) {
        _helper('api')->error($e->getMessage());
      }

      return $group;
    }
    /**
     *
     */
    public function getGroups($request, $workspace = false)
    {
      $groups = Group::all();
      if ($workspace) {
        $groups = Group::where('workspace_id', $workspace)->get();
      }

      return $groups;
    }

    public function deleteGroup($params)
    {
      $groupId = $params['id'] ?? false;
      if ($groupId) {
        $group = Group::findOrFail($groupId);
        if ($group) {
          return $group->delete();
        }
      }

      return false;
    }

    public function getPlanningMonthes($projectId, $deadlineDate) {
        $planningMonthes = [];
        $firstTask = DB::table('tasks')->where('project_id', $projectId)->orderBy('start_date')->first();
        $previousMonthes = 1;
        $firstTaskMonth = 1;
        $projectDeadlineMonth = 1;
        $nextMonthes = 1;
        $firstTaskMonthName = 'january';
        $projectDeadlineMonthName = 'january';
        if ($deadlineDate && $firstTask) {
            $firstTaskDeadline = Carbon::createFromFormat('Y-m-d H:i:s', $firstTask->start_date);
            $projectDeadline = Carbon::createFromFormat('Y-m-d H:i:s', $deadlineDate);
            $projectDeadlineMonth = $projectDeadline->month;
            $firstTaskMonth = $firstTaskDeadline->month;

            //$firstTaskMonth = 1;
            //$projectDeadlineMonth = 1;

            if ($firstTaskMonth > 0) {
              $firstTaskMonthName =  $this->getMonthName($firstTaskMonth);
              $previousMonthes = $firstTaskMonth;
              /*$i = 0;
              while ($i < 3) {
                  $previousMonthes--;
                  if ($previousMonthes < 0) {
                    $previousMonthes = 12;
                  }
                  if ($previousMonthes > 0 && !isset($planningMonthes[$previousMonthes])) {
                    $planningMonthes[$previousMonthes] = $this->getMonthName($previousMonthes);
                  }
                  $i++;
              }*/
              $planningMonthes[$firstTaskMonth] = $firstTaskMonthName;
            }

            if ($projectDeadlineMonth > 0) {
                $projectDeadlineMonthName =  $this->getMonthName($projectDeadlineMonth);
                $nextMonthes = $projectDeadlineMonth;
                $i = 0;
                while ($i < 3) {
                  $nextMonthes++;
                  if ($nextMonthes > 12) {
                    $nextMonthes = 1;
                  }
                  if ($nextMonthes > 0 && !isset($planningMonthes[$nextMonthes])) {
                    $planningMonthes[$nextMonthes] = $this->getMonthName($nextMonthes);
                  }
                  $i++;
                }
                $planningMonthes[$projectDeadlineMonth] = $projectDeadlineMonthName;
            }
        }

        return $planningMonthes;
    }

    public function getMonthName($monthKey) {
      $monthName = '';
      if (!empty($this->monthes[$monthKey])) {
        $monthName = $this->monthes[$monthKey];
      }
      return $monthName;
    }
}
