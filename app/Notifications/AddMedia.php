<?php

namespace App\Notifications;

use App\Channels\SendinblueChannel;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Project;
use App\Models\ProjectFile;

class AddMedia extends Notification
{
    use Queueable;

    private $kolabProject;
    private $media;
    private $mediaOwner;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Project $kolabProject, ProjectFile $media, $mediaOwner)
    {
        $this->kolabProject = $kolabProject;
        $this->media = $media;
        $this->mediaOwner = $mediaOwner;
    }

    public function via($notifiable)
    {
        return [SendinblueChannel::class];
    }

    public function toSendinblue(User $user)
    {
        $project = $this->kolabProject;
        $projectName = $project->name;
        $projectOwner = $project->creator;
        $mediaOwner = $this->mediaOwner;
        $mediaName = $this->media->name;
        $mediaDate = $this->media->created_at;
        $projectUrl = $project->url;
        $projectId = $project->id;

        if ($mediaDate) {
            $dayName = ucfirst($mediaDate->locale('fr')->dayName);
            $day = $mediaDate->day;
            $monthName = ucfirst($mediaDate->locale('fr')->monthName);
            $hour = $mediaDate->hour;
            if ($hour < 10) {
                $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
            }
            $minute = $mediaDate->minute;
            if ($minute < 10) {
                $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);
            }

            $mediaDate = $dayName . ' ' . $day . ' ' . $monthName . ' ' . $hour . 'h' . $minute;
        }

        $receivers = [$user->email];
        $params = [
            'PROJECT_TITLE'=> $projectName,
            'PROJECT_OWNER'=> $projectOwner,
            'MEDIA_OWNER' => $mediaOwner,
            'MEDIA_NAME' => $mediaName,
            'MEDIA_DATE' => $mediaDate,
            'PROJECT_URL' => $projectUrl,
            'PROJECT_ID' => $projectId,
        ];

        return [
            34, // template_id
            $receivers, // receivers
            $params, // params
            null // attachment
        ];
    }
}
