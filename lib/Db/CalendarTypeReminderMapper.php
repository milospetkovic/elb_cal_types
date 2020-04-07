<?php


namespace OCA\ElbCalTypes\Db;


use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
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

    public function getAssignedRemindersForCalendarTypes(array $arrayOfCalTypeIds)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('ctr.id as link_id', 'ctr.fk_elb_cal_type as cal_type_id',
            'ctr.fk_elb_def_reminder as cal_def_reminder_id', 'cdf.title as cal_def_reminder_title', 'cdf.minutes_before_event')
            ->from('elb_cal_type_reminders', 'ctr')
            ->rightJoin('ctr', 'elb_calendar_types', 'ct', $qb->expr()->eq('ctr.fk_elb_cal_type', 'ct.id'))
            ->rightJoin('ctr', 'elb_cal_def_reminders', 'cdf', $qb->expr()->eq('ctr.fk_elb_def_reminder', 'cdf.id'))
            ->where('ctr.id > 0' )
            ->groupBy('ctr.id')
            ->orderBy('cdf.minutes_before_event', 'ASC');
        if (count($arrayOfCalTypeIds)) {
            $qb->andWhere($qb->expr()->in('ctr.fk_elb_cal_type', $qb->createNamedParameter($arrayOfCalTypeIds, IQueryBuilder::PARAM_INT_ARRAY)));
        }
        return $qb->execute()->fetchAll();
    }

}