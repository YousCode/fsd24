<?php

namespace App\Http\Services\Explorer;

use App\Models\ExplorerDrive;
use App\Models\ExplorerDriveLink;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Embed\Embed;

class ExplorerDriveService
{
    /**
     * @param User $user
     * @param $driveId
     * @return ExplorerDrive|ExplorerDrive[]|Collection|Model|null
     * @throws Exception
     */
    public function getDrive(User $user, $driveId)
    {
        $drive = ExplorerDrive::find($driveId);

        if ($this->checkUserDriveAccess($drive, $user)) {
            return $drive;
        } else {
            throw new Exception("User Doesn't have access to the drive");
        }

    }

    public function addLink(ExplorerDrive $drive, $linkUrl, $isShared = false, $contactId = false): ExplorerDriveLink
    {
        $link = new ExplorerDriveLink();
        $embed = new Embed();

        $link->id_drive = $drive->id;
        $link->link = $linkUrl;
        $link->name = ($this->getThumbnailName($embed, $linkUrl)) ? $this->getThumbnailName($embed, $linkUrl) : $linkUrl;
        $link->miniature = $this->getThumbnail($embed, $linkUrl);
        if (\Auth::user()) {
            $link->created_by = \Auth::user()->id;
        } else if ($isShared && $contactId > 0) {
            $link->created_by = $contactId;
        }
        $link->save();

        return $link;
    }

    public function getThumbnail($embed, $link) {
        $infos = $embed->get($link);
        if (!empty($infos->image)) {
            return "";
        }

        return $infos->image;
    }

    public function getThumbnailName($embed, $link) {
        $infos = $embed->get($link);
        if (!empty($infos->title)) {
            return "";
        }
        return $infos->title;
    }

    /**
     * @param ExplorerDrive $drive
     * @param User $user
     * @return bool
     */
    private function checkUserDriveAccess(ExplorerDrive $drive, User $user): bool
    {
        return $drive->missionProposition()->get('freelance_id') == $user->id || $drive->missionProposition()->get('client_id') == $user->id;
    }
}
