<?php

namespace App\Http\Helpers;

use App\Models\Appointment;
use App\Models\Project;
use App\Models\Task;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DashboardHelper
{
  public function get($userId, $params)
  {
    return [
      'last_message'=> $this->getLastMessage($userId),
      'tasks' => $this->getDashboardTasks($userId, $params),
      'closed_tasks' => $this->getClosedTasks($userId, $params),
      'waiting_tasks' => $this->getWaitingTasks($userId, $params),
      'appointments' => $this->getDashboardAppointments($userId),
      // 'waiting_appointments' => $this->getWaitingAppointments($userId, $params),
    ];
  }

  // -- Useful

  /**
   * Return all the future tasks of the connected user
   *
   * @param $userId
   * @return Task[]|Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
   */

   private function getBaseTasks($userId, $edit = false) {
    $user = User::findOrFail($userId);
    $workspaceId = false;
    if ($user) {
      $workspaceId = $user->currentWorkspace;
    }
      $tasks = Task::query()
        ->select([
          'tasks.id as id',
          'tasks.name as name',
          'tasks.note as note',
          'tasks.status as status',
          'tasks.accepted as accepted',
          'projects.id as project_id',
          'projects.name as project_name',
          'project_categories.name as project_category',
          'project_categories.color as project_category_color',
          'task_types.name as task_type',
          'tasks.start_date as start_date',
          'tasks.end_date as end_date',
          'tasks.closed_at as closed_at',
          'tasks.created_at as created_at',
          'tasks.updated_at as updated_at',
          'users.firstname as created_by',
          'users.avatar as profile',
        ])
        ->join('task_talent', 'tasks.id', '=', 'task_talent.task_id')
        ->join('projects', 'tasks.project_id', '=', 'projects.id')
        ->join('project_categories', 'projects.category_id', 'project_categories.id')
        ->join('task_type_task', 'tasks.id', '=', 'task_type_task.task_id')
        ->join('task_types', 'task_type_task.task_type_id', '=', 'task_types.id')
        ->join('users', 'tasks.created_by', '=', 'users.id')
        ->leftJoin('workspaces', 'projects.workspace_id', '=', 'workspaces.id')
        ->with('talents')
        //->whereNot(function (Builder $query) { $query->where('category_id', 9); })
        ->where('workspaces.id', '=', $workspaceId)
        ->where(function(Builder $query) use ($userId) {
          $query->where('task_talent.user_id', '=', $userId)->orWhere('tasks.created_by', '=', $userId);
        });
        if ($tasks && $workspaceId) {
          return $tasks;
        }

      return false;
   }

  private function getDashboardTasks($userId, $params)
  {
    $output = [];
    $today = Carbon::today();
    $edit = $params['edit'] ?? false;
    $tasks = $this->getBaseTasks($userId, $edit);
    /*$appointments = Appointment::query()
      ->select([
        'appointments.id as id',
        'appointments.lastname as lastname',
        'appointments.firstname as firstname',
        'appointments.datetime as datetime',
        'appointments.email as email',
        'appointments.location as location',
        'appointments.user_id as user_id'

      ])
      ->join('appointment_talent', 'appointments.id', '=', 'appointment_talent.appointment_id')
      ->join('users', 'appointments.created_by', '=', 'user.id')
      ->with('talents')
      ->where('appointment_talent.user_id', '=', $userId);*/

      $progressTask = $tasks->orderBy('tasks.created_at', 'desc')->groupBy('tasks.id')->having('tasks.status', '=', 'PROGRESS')->get();
      $progressTask = $progressTask->filter(function($task) {
      $now = Carbon::now();
      $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $task->start_date);
      $startDate = Carbon::create($startDate->format('Y'), $startDate->format('m'), $startDate->format('d'), 00, 00, 00);
      $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $task->end_date);
      $endDate = Carbon::create($endDate->format('Y'), $endDate->format('m'), $endDate->format('d'), 23, 59, 59);

      return $now->between($startDate, $endDate);
    });

    $progressTasks = [];

    if (!empty($progressTask)) {
      foreach($progressTask as $task) {
        $progressTasks[] = $task;
      }
    }
    
    $output = $progressTasks;

    return $output;
  }

  /**
   * Return all the future appointments of the connected user
   *
   * @param $userId
   * @return Appointment[]|Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
   */
  private function getDashboardAppointments($userId)
  {
    return Appointment::with('talents')
      ->with('contacts')
      ->with('user')
      ->where(function ($q) use ($userId) {
        $q->where('created_by', '=', $userId)
          ->orWhereRelation('talents', 'id', '=', $userId);
      })
      ->where('datetime', '>=', date('Y-m-d').' 00:00:00')
      ->orderBy('datetime')
      ->get();
  }

  private function getClosedTasks($userId, $params)
  {
    $tasks = $this->getBaseTasks($userId);
    $now = Carbon::now();
    $monday = $now;
    $sunday = $now;
    if ($now->startOfWeek()) {
      $monday = $now->startOfWeek()->format('Y-m-d H:i:s');
    }
    if ($now->endOfWeek()) {
      $sunday = $now->endOfWeek()->format('Y-m-d H:i:s');
    }
    $closedtasks = $tasks
      ->where(function ($query) {
        $query->whereNull('tasks.status')->orWhere('tasks.status', '=', 'CLOSED');
      });
    $closedtasks->whereBetween('tasks.closed_at', [$monday, $sunday]);

    return $closedtasks->orderBy('tasks.closed_at', 'desc')->get();
  }

  private function getWaitingTasks($userId, $params)
  {
    $tasks = [];
    $baseTasks = $this->getBaseTasks($userId);
    $waitingtasks = $baseTasks->where('tasks.status', '=', 'PROGRESS')->orderBy('id')->get();
      
      $waitingtasks = $waitingtasks->filter(function($task) {
        $tomorrow = Carbon::tomorrow();
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $task->start_date);
        $startDate = Carbon::create($startDate->format('Y'), $startDate->format('m'), $startDate->format('d'), 00, 00, 00);
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $task->end_date);
        $endDate = Carbon::create($endDate->format('Y'), $endDate->format('m'), $endDate->format('d'), 23, 59, 59);
    
        return $tomorrow->between($startDate, $endDate);
      });

      if (!empty($waitingtasks)) {
        foreach($waitingtasks as $task) {
          $tasks[] = $task;
        }
      }

    return $tasks;
  }

  // private function getWaitingAppointments($userId, $params)
  // {
  //   $waitingAppointments = Appointment::query()
  //     ->join('appointment_talent', 'appointments.id', '=', 'appointment_talent.appointment_id')
  //     ->where('appointment_talent.user_id', '=', $userId)
  //     ->where(function ($query) {
  //       $query->whereNull('appointments.datetime')
  //         ->orWhere('appointments.datetime', '>', Carbon::today());
  //     });

  //   return $waitingAppointments->count();
  // }
  private function getLastMessage($userId){
      $lastConversation = DB::table('explorer_mission_conversation_messages')->select('id_conversation')->where('created_by', '=', $userId)->orderBy('created_at', 'DESC')->first();
      if (!empty($lastConversation) && $lastConversation->id_conversation > 0) {
          $lastMessage = DB::table('explorer_mission_conversation_messages')->select('*')->where('id_conversation', '=', $lastConversation->id_conversation)->orderBy('created_at', 'DESC')->first();
          if (!empty($lastMessage)) {
              return $lastMessage;
          }
      }
      return false;
  }
}
