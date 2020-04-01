<?php


namespace OCA\ElbCalTypes\Db;


use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class CalendarTypeReminder extends Entity implements JsonSerializable
{
    protected $fkElbCalType;
    protected $fkGroupFolder;
    protected $userAuthor;
    protected $createdAt;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'fk_elb_cal_type' => $this->fkElbCalType,
            'fk_group_folder' => $this->fkGroupFolder,
            'user_author' => $this->userAuthor,
            'created_at' => $this->createdAt,
        ];
    }

}