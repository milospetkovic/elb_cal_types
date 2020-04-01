<?php


namespace OCA\ElbCalTypes\Db;


use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class CalendarTypeReminder extends Entity implements JsonSerializable
{
    protected $fkElbCalType;
    protected $fkElbDefReminder;
    protected $userAuthor;
    protected $createdAt;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'fk_elb_cal_type' => $this->fkElbCalType,
            'fk_elb_def_reminder' => $this->fkElbDefReminder,
            'user_author' => $this->userAuthor,
            'created_at' => $this->createdAt,
        ];
    }

}