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

}