<?php


namespace OCA\ElbCalTypes\Db;


use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class CalendarTypeReminderMapper extends QBMapper
{
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, 'elb_cal_type_reminders', CalendarTypeReminder::class);
    }

    /**
     * Fetch all calendar types reminders
     *
     * @return array
     */
    public function findAll(): array
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('elb_cal_type_reminders');
        return $this->findEntities($qb);
    }

}