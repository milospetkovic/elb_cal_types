<?php
namespace OCA\ElbCalTypes\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class CalendarDefaultReminders extends Entity implements JsonSerializable {

    protected $createdAt;
    protected $userAuthor;
    protected $title;
    protected $minutesBeforeEvent;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'user_author' => $this->userAuthor,
            'title' => $this->title,
            'minutes_before_event' => $this->minutesBeforeEvent
        ];
    }
}