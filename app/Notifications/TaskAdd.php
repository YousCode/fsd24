<?php

namespace App\Notifications;

use App\Channels\SendinblueChannel;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAdd extends Notification
{
    use Queueable;
    private $namefrom;
    private $nameto;
    private $urlProject;
    private $taskName;
    private $projectName;
    private $projectId;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($namefrom, $nameto, $urlProject, $taskName, $projectName, $projectId)
    {
        $this->namefrom = $namefrom ?? "";
        $this->nameto = $nameto ?? "";
        $this->urlProject = $urlProject ?? "";
        $this->projectName = $projectName ?? "";
        $this->projectId = $projectId ?? "";
        $this->taskName = $taskName ?? "";
    }

    public function via($notifiable)
    {
        return [SendinblueChannel::class];
    }

    public function toSendinblue(User $user)
    {
        $receivers = [$user->email];
        $params = [
            'NAMETO' => $this->nameto,
            'NAMEFROM' => $this->namefrom,
            'URL_PROJECT' => $this->urlProject,
            'PROJECT_NAME' => $this->projectName,
            'TASK_NAME' => $this->taskName,
            'PROJECT_ID' => $this->projectId,
        ];

        return [
            18, // template_id
            $receivers, // receivers
            $params, // params
            null // attachment
        ];
    }
}
