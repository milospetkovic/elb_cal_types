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
        $qb->select('cte.id as cal_type_event_id', 'cte.title as event_title', 'cte.description as event_description',
            'cte.event_datetime', 'cte.executed as event_executed',
            'eeu.fk_user as assigned_user_id',
            'eer.fk_cal_def_reminder as event_def_reminder_id',
            'ecdr.title as def_reminder_title')
            ->from('elb_cal_type_events', 'cte')
            ->leftJoin('cte', 'elb_event_users', 'eeu', $qb->expr()->eq('eeu.fk_cal_type_event', 'cte.id'))
            ->leftJoin('eeu', 'elb_event_reminders', 'eer', $qb->expr()->eq('eer.fk_cal_type_event', 'eeu.fk_cal_type_event'))
            ->leftJoin('eeu', 'elb_cal_def_reminders', 'ecdr', $qb->expr()->eq('ecdr.id', 'eer.fk_cal_def_reminder'))
            ->where($qb->expr()->eq('cte.fk_gf_cal_type', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        return $qb->execute()->fetchAll();
    }

}