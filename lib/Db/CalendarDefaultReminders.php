<?php
namespace OCA\ElbCalTypes\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class CalendarDefaultReminders extends Entity implements JsonSerializable {

    protected $createdAt;
    protected $modifiedAt;
    protected $userAuthor;
    protected $userModifier;
    protected $title;
    protected $minutesBeforeEvent;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'user_author' => $this->userAuthor,
            'user_modifier' => $this->userModifier,
            'title' => $this->title,
            'slug' => $this->slug,
            'minutes_before_event' => $this->minutesBeforeEvent
        ];
    }
}