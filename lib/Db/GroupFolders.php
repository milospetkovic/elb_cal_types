<?php


namespace OCA\ElbCalTypes\Db;


use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class GroupFolders extends Entity implements JsonSerializable
{
    protected $folderId;
    protected $mountPoint;
    protected $quota;
    protected $acl;

    public function jsonSerialize(): array
    {
        return [
            'folder_id' => $this->folderId,
            'mount_point' => $this->mountPoint,
            'quota' => $this->quota,
            'acl' => $this->acl,
        ];
    }

}