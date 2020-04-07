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
            ->leftJoin('ctr', 'elb_calendar_types', 'ct', $qb->expr()->eq('ctr.fk_elb_cal_type', 'ct.id'))
            ->leftJoin('ctr', 'elb_cal_def_reminders', 'cdf', $qb->expr()->eq('ctr.fk_elb_def_reminder', 'cdf.id'))
            ->where('ctr.id > 0' );
        if (count($arrayOfCalTypeIds)) {
            //$qb->andWhere($qb->expr()->in('ctr.fk_elb_cal_type', $qb->createNamedParameter($arrayOfCalTypeIds, IQueryBuilder::PARAM_INT_ARRAY)));
            $qb->andWhere('ctr.fk_elb_cal_type IN ('. implode(',', $arrayOfCalTypeIds).')');
        }
            $qb->groupBy('ctr.id')
            ->orderBy('cdf.minutes_before_event', 'ASC');

        return $qb->execute()->fetchAll();
    }

}