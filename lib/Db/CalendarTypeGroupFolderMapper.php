<?php


namespace OCA\ElbCalTypes\Db;


use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class CalendarTypeGroupFolderMapper extends QBMapper
{
    private $table_name = 'elb_gf_cal_types';

    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, $this->table_name, CalendarTypeGroupFolder::class);
    }

    /**
     * Fetch all calendar types group folders
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

    public function returnAssignedGroupFoldersForCalendarTypes()
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('gfct.id as link_id', 'ctr.fk_elb_cal_type as cal_type_id',
            'ctr.fk_elb_def_reminder as cal_def_reminder_id', 'cdf.title as cal_def_reminder_title', 'cdf.minutes_before_event')
            ->from('elb_gf_cal_types', 'gfct')
            ->rightJoin('ctr', 'elb_calendar_types', 'ct', $qb->expr()->eq('ctr.fk_elb_cal_type', 'ct.id'))
            ->rightJoin('ctr', 'elb_cal_def_reminders', 'cdf', $qb->expr()->eq('ctr.fk_elb_def_reminder', 'cdf.id'))
            ->where('ctr.id > 0' )
            ->groupBy('ctr.id')
            ->orderBy('cdf.minutes_before_event', 'ASC');
        return $qb->execute()->fetchAll();
    }

}