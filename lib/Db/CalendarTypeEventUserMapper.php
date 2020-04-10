<?php


namespace OCA\ElbCalTypes\Db;


use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class CalendarTypeEventUserMapper extends QBMapper
{
    private $table_name = 'elb_event_users';

    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, $this->table_name, CalendarTypeEventUser::class);
    }

}