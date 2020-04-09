<?php


namespace OCA\ElbCalTypes\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class CalendarTypeEventReminder extends Entity implements JsonSerializable
{
    protected $fkCalTypeEvent;
    protected $fkCalDefReminder;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'fk_cal_type_event' => $this->fkCalTypeEvent,
            'fk_cal_def_reminder' => $this->fkCalDefReminder,
        ];
    }
}
