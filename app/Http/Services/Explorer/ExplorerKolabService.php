<?php

namespace App\Http\Services\Explorer;


use App\Events\ExplorerMessage;
use App\Models\Client;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\Workspace;
use App\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;

class ExplorerKolabService
{
    public function createKolabProjectFromExplorerProposition(ExplorerMissionProposition $explorerMissionProposition)
    {
        $explorerMission = $explorerMissionProposition->explorerMission;

        $project = new Project();
        $user = User::findOrFail($explorerMissionProposition->client_id);
        $freelance = User::findOrFail($explorerMissionProposition->freelance_id);
        $project->name = $explorerMission->name;
        $project->production = "Kolab";
        $project->client_id = $this->findOrCreateKolabClientFromExplorerUser($user);
        $project->created_by = $this->findOrCreateKolabClientFromExplorerUser($user);
        $project->updated_by = $this->findOrCreateKolabClientFromExplorerUser($user);
        $project->client_phone = $explorerMissionProposition->client->phone;
        $project->client_email = $explorerMissionProposition->client->email;
        if($user){
            $clientWorkspaceId = $user->currentWorkspace;
            if ($clientWorkspaceId) {
                $clientWorkspace = Workspace::findOrFail($clientWorkspaceId);
                //$freeWorkspaceId = $freelance->currentWorkspace
                if (!$clientWorkspace->members()->where('users.id', $freelance->id)->exists()) {
                    $clientWorkspace->members()->attach($freelance);
                    $clientWorkspace->save();
                }
                $project->workspace_id = $clientWorkspaceId;
                $freelance->current_workspace_id = $clientWorkspaceId;
            }
        }
        $project->category_id = $this->findOrCreateKolabProjectCategory("mission-explorer");

        switch ($explorerMission->deadline) {
            case  Carbon::parse($explorerMission->created_at)->add(24,'hours')->format('Y-m-d 00:00:00'):
                $project->date_deadline =  Carbon::parse($explorerMission->created_at)->add(24,'hours')->format('Y-m-d 00:00:00');
                break;
            case  Carbon::parse($explorerMission->created_at)->add(6,'days')->format('Y-m-d 00:00:00'):
                $project->date_deadline = Carbon::parse($explorerMission->created_at)->add(6,'days')->format('Y-m-d 00:00:00');
                break;
            case Carbon::parse($explorerMission->created_at)->add(14,'days')->format('Y-m-d 00:00:00'):
                $project->date_deadline =  Carbon::parse($explorerMission->created_at)->add(14,'days')->format('Y-m-d 00:00:00');
                break;
            case Carbon::parse($explorerMission->created_at)->add(24,'days')->format('Y-m-d 00:00:00'):
                $project->date_deadline =  Carbon::parse($explorerMission->created_at)->add(24,'days')->format('Y-m-d 00:00:00');
                break;
            default:
                $project->date_deadline = Carbon::now()->format('Y-m-d 00:00:00');
                break;
        }
        //dd();
        $project->save();


        $project->talents()->sync($explorerMissionProposition->freelance_id);
        $project->save();


        $explorerMissionProposition->kolab_project_id = $project->id;
        $explorerMissionProposition->save();
    }

    public function createKolabPlanningTask(ExplorerMissionQuote $explorerMissionQuote)
    {
        try {
            if ($explorerMissionQuote->deadline != null && $explorerMissionQuote->deadline != '') {
                $kolabTask = new Task();

                $kolabTask->name = "Devis de la mission " . $explorerMissionQuote->proposition->mission->name . "par " . $explorerMissionQuote->proposition->freelance_id;
                $kolabTask->project_id = $explorerMissionQuote->proposition->kolabProject->id;
                $kolabTask->note = json_encode([['note' => $explorerMissionQuote->comments, 'state' => false]]);
                $kolabTask->start_date = Carbon::now()->format('Y-m-d 00:00:00');
                $kolabTask->end_date = Carbon::createFromFormat('Y-m-d H:i:s', $explorerMissionQuote->deadline);

                /*switch ($explorerMissionQuote->deadline) {
                    case "Le lendemain":
                        $kolabTask->end_date = DateTime::createFromFormat('Y-m-d', Carbon::now()->add(24,'hours')->format('Y-m-d'))->format('Y-m-d');
                        break;
                    case "Dans 2 jours":
                        $kolabTask->end_date = DateTime::createFromFormat('Y-m-d', Carbon::now()->add(6,'days')->format('Y-m-d'))->format('Y-m-d');
                        break;
                    case "Dans 3 jours":
                        $kolabTask->end_date = DateTime::createFromFormat('Y-m-d', Carbon::now()->add(14,'days')->format('Y-m-d'))->format('Y-m-d');
                        break;
                    case "Dans 4 jours":
                        $kolabTask->end_date = DateTime::createFromFormat('Y-m-d', Carbon::now()->add(24,'days')->format('Y-m-d'))->format('Y-m-d');
                        break;
                }*/

                $kolabTask->created_by = $explorerMissionQuote->proposition->client_id;
                $kolabTask->updated_by = $explorerMissionQuote->proposition->client_id;
                $kolabTask->status = 'PROGRESS';
                $kolabTask->accepted = 1;
                $kolabTask->save();
                $kolabTask->taskTypes()->sync(TaskType::where('id', '=', $explorerMissionQuote->proposition->mission->type)->first());
                $kolabTask->talents()->sync($explorerMissionQuote->proposition->freelance_id);

                $kolabTask->save();

                $explorerMissionQuote->kolab_task_id = $kolabTask->id;
                $explorerMissionQuote->save();
            }
        } catch (Exception $e) {
        }
    }

    /**
     * Find or create a new Client in the database and return the client ID
     * @param User $explorerClientUser
     * @return mixed
     */
    private function findOrCreateKolabClientFromExplorerUser(User $explorerClientUser)
    {
        $client = Client::where('explorer_client_id', '=', $explorerClientUser->id)->first();

        if ($client == null) {
            $client = Client::where('name', '=', $explorerClientUser->name)->first();
        }
        if ($client == null) {
            $client = new Client();
            $client->explorer_client_id = $explorerClientUser->id;
            $client->name = $explorerClientUser->name;
            $client->created_by = $explorerClientUser->id;
            $client->updated_by = $explorerClientUser->id;

            try {
                event(new ExplorerMessage());
                $client->save();
            } catch (Exception $e) {
            }
        }

        return $client->id;
    }

    private function findOrCreateKolabProjectCategory($explorerMissionType)
    {
        try {
            $projectCategory = ProjectCategory::where('name', '=', $explorerMissionType)->first();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        if ($projectCategory == null) {
            $projectCategory = new ProjectCategory();
            $projectCategory->name = $explorerMissionType;
        }
        event(new ExplorerMessage());
        $projectCategory->save();

        return $projectCategory->id;
    }
    private function findOrCreateKolabTaskType($explorerTaskType)
    {
        try {
            $taskType = TaskType::where('name', '=', $explorerTaskType)->first();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $taskType->save();

        return $taskType;
    }
}
