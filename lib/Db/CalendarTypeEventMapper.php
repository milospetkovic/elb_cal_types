<?php


namespace OCA\ElbCalTypes\Db;


use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class CalendarTypeEventMapper extends QBMapper
{
    private $table_name = 'elb_cal_type_events';

    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, $this->table_name, CalendarTypeEvent::class);
    }

    /**
     * Fetch all calendar type event records
     *
     * @return array
     */
    public function findAll(): array
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->table_name);
        return $this->findEntities($qb);
    }

    public function fetchCalendarTypeEvents($id)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('cte.id as cal_type_event_id', 'cte.event_datetime', 'eer.fk_cal_def_reminder')
            ->from('elb_cal_type_events', 'cte')
            ->leftJoin('cte', 'elb_event_users', 'eeu', $qb->expr()->eq('eeu.fk_cal_type_event', 'cte.id'))
            ->leftJoin('eeu', 'elb_event_reminders', 'eer', $qb->expr()->eq('eer.fk_cal_type_event', 'eeu.fk_cal_type_event'))
            ->where($qb->expr()->eq('cte.fk_elb_cal_type', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        return $qb->execute()->fetchAll();
    }

}