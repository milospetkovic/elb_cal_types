<?php


namespace OCA\ElbCalTypes\Db;


use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class CalendarTypeEventReminderMapper extends QBMapper
{

    private $table_name = 'elb_event_reminders';

    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, $this->table_name, CalendarTypeEventReminder::class);
    }

}