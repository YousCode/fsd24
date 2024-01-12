<?php

namespace App\Notifications;

use App\Channels\SendinblueChannel;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Project;
use App\Models\ExplorerDriveLink;

class AddLink extends Notification
{
    use Queueable;

    private $kolabProject;
    private $driveLink;
    private $driveLinkOwner;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Project $kolabProject, ExplorerDriveLink $driveLink, $driveLinkOwner)
    {
        $this->kolabProject = $kolabProject;
        $this->driveLink = $driveLink;
        $this->driveLinkOwner = $driveLinkOwner;
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
        $driveLinkOwner = $this->driveLinkOwner;
        $driveLinkUrl = $this->driveLink->link;
        $driveLinkDate = $this->driveLink->created_at;
        $projectUrl = $project->url;
        $projectId = $project->id;

        if ($driveLinkDate) {
            $dayName = ucfirst($driveLinkDate->locale('fr')->dayName);
            $day = $driveLinkDate->day;
            $monthName = ucfirst($driveLinkDate->locale('fr')->monthName);
            $hour = $driveLinkDate->hour;
            if ($hour < 10) {
                $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
            }
            $minute = $driveLinkDate->minute;
            if ($minute < 10) {
                $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);
            }

            $driveLinkDate = $dayName . ' ' . $day . ' ' . $monthName . ' ' . $hour . 'h' . $minute;
        }

        $receivers = [$user->email];
        $params = [
            'PROJECT_TITLE'=> $projectName,
            'PROJECT_OWNER'=> $projectOwner,
            'DRIVE_LINK_OWNER' => $driveLinkOwner,
            'DRIVE_LINK_URL' => $driveLinkUrl,
            'DRIVE_LINK_DATE' => $driveLinkDate,
            'PROJECT_URL' => $projectUrl,
            'PROJECT_ID' => $projectId,
        ];

        return [
            20, // template_id
            $receivers, // receivers
            $params, // params
            null // attachment
        ];
    }
}
