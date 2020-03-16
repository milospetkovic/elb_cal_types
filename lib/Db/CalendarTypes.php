<?php
namespace OCA\ElbCalTypes\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class CalendarTypes extends Entity implements JsonSerializable {

    protected $title;
    protected $userId;
    protected $slug;
    protected $description;

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description
        ];
    }
}