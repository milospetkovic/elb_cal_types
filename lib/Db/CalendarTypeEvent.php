<?php


namespace OCA\ElbCalTypes\Db;


use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class CalendarTypeEvent extends Entity implements JsonSerializable
{
    protected $fkGfCalType;
    protected $createdAt;
    protected $userAuthor;
    protected $title;
    protected $description;
    protected $eventDatetime;
    protected $executed;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'fk_gf_cal_type' => $this->fkGfCalType,
            'created_at' => $this->createdAt,
            'user_author' => $this->userAuthor,
            'title' => $this->title,
            'description' => $this->description,
            'event_datetime' => $this->eventDatetime,
            'executed' => $this->executed,
        ];
    }

}