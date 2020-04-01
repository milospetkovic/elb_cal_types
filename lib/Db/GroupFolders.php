<?php


namespace OCA\ElbCalTypes\Db;


use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class GroupFolders extends Entity implements JsonSerializable
{
    protected $mountPoint;

    public function jsonSerialize(): array
    {
        return [
            'folder_id' => $this->id,
            'mount_point' => $this->mountPoint,
        ];
    }

}