<?php
namespace OCA\ElbCalTypes\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class CalendarTypes extends Entity implements JsonSerializable {

    protected $title;
    protected $slug;
    protected $description;

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description
        ];
    }
}