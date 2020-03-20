<?php


namespace OCA\ElbCalTypes\Db;


use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class CalendarDefaultRemindersMapper extends QBMapper
{
    /**
     * CalendarDefaultRemindersMapper constructor.
     * @param IDBConnection $db
     */
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, 'elb_cal_def_reminders', CalendarDefaultReminders::class);
    }

    /**
     * Fetch default calendar reminders
     *
     * @param bool $entities
     * @return array
     */
    public function findAll($entities=true): array
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('elb_cal_def_reminders');

        if ($entities) {
            return $this->findEntities($qb);
        }
        return $qb->execute()->fetchAll();
    }

}