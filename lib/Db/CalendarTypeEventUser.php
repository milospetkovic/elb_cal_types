<?php


namespace OCA\ElbCalTypes\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class CalendarTypeEventUser extends Entity implements JsonSerializable
{
    protected $fkCalTypeEvent;
    protected $fkUser;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'fk_cal_type_event' => $this->fkCalTypeEvent,
            'fk_user' => $this->fkUser,
        ];
    }

}