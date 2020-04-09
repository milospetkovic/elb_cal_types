<?php


namespace OCA\ElbCalTypes\Db;


use OCP\AppFramework\Db\QBMapper;
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

}