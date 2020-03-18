<?php
namespace OCA\ElbCalTypes\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class CalendarTypes extends Entity implements JsonSerializable {

    protected $createdAt;
    protected $modifiedAt;
    protected $title;
    protected $userAuthor;
    protected $userModifier;
    protected $slug;
    protected $description;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'user_author' => $this->userAuthor,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description
        ];
    }
}