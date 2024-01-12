<?php

namespace App\Http\Helpers;

use App\Http\Helpers\NotificationHelper;
use App\Http\Helpers\TaskHelper;
use App\Models\Portfolio;
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
use App\Models\Task;

/**
 * Class ProjectHelper
 * @package App\Http\Helpers
 */
class PortfolioHelper
{

    /**
     * Get all projects
     * @param $request
     * @return Project[]|Collection [Object]  Project
     */
    public function all($request = [])
    {
        $portfolio = Portfolio::query()->select(['*', DB::raw('YEAR(date_deadline) year, MONTH(date_deadline) month')]);

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
    public function search($term = null)
    {
        if ($term) {
            return Project::where('name', 'LIKE', "%$term%")->get();
        } else {
            return Project::all();
        }
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

        $project->name = $projectParams['name'];
        $project->production = $projectParams['production'];
        $project->client_id = $this->findOrCreateClient($params);
        $project->client_phone = $projectParams['client_phone'];
        $project->client_email = $projectParams['client_email'];
        $project->responsable_name = $projectParams['responsable_name'];
        $project->responsable_phone = $projectParams['responsable_phone'];
        $project->responsable_email = $projectParams['responsable_email'];
        $project->category_id = $projectParams['category_id'];
        $project->date_deadline = Carbon::parse($projectParams['date_deadline'])->add(1, 'day')->format('Y-m-d 00:00:00');

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
    public function getPlanning($id)
    {
        // Helpers
        $taskHelper = new TaskHelper();

        // Project tasks
        $tasks = $taskHelper->getBase();
        $projectTasks = $tasks->where('tasks.project_id', $id)
            ->orderBy('start_date')
            ->get();

        if(count($projectTasks) <= 0 ){
            return [];
        }

        // Project start and end informations
        $result['project_start'] = $projectTasks[0]->start_date;
        $result['project_end'] = Carbon::parse($projectTasks->last()->end_date)->add(1, 'day');

        try {
            // Create a Date Interval Iterator to create the planning task array
            $period = new DatePeriod(
                new DateTime($result['project_start']),
                new DateInterval('P1D'),
                new DateTime($result['project_end'])
            );

            setlocale(LC_TIME, 'fr_FR.UTF-8');

            // Populating the Planning Array
            foreach ($period as $date) {
                $_date = $date->format('Y-m-d');

                // Initializing empty array for the date in case there is no task at the date
                $result['project_tasks'][$_date] = [];

                foreach ($projectTasks as $projectTask) {
                    if (($date >= new DateTime($projectTask->start_date)) && ($date <= new DateTime($projectTask->end_date))) {
                        // Add the task if the current date is in the task interval
                        $result['project_tasks'][$_date][] = $projectTask;
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

    public function addFiles($id, $files)
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
                $_file->save();
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
                unlink($file->path);
                $file->delete();
            } catch (Exception $e) {
                _info($e->getMessage());
            }
        }

        return true;
    }

    // -- USEFUL

    /**
     * Find or create a new Client in the database and return the client ID
     * @param $params
     * @return mixed
     */
    private function findOrCreateClient($params)
    {
        $client = Client::where('name', '=', $params['project']['client_name'])->first();

        if ($client == null) {
            $client = new Client();
            $client->name = $params['project']['client_name'];
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
