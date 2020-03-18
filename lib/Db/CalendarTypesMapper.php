<?php
namespace OCA\ElbCalTypes\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class CalendarTypesMapper extends QBMapper
{
    /**
     * CalendarTypesMapper constructor.
     * @param IDBConnection $db
     */
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, 'elb_calendar_types', CalendarTypes::class);
    }

    /**
     * Find calendar type by it's id
     *
     * @param int $id
     * @return Entity|CalendarTypes
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     */
    public function find(int $id): CalendarTypes
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('elb_calendar_types')
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        return $this->findEntity($qb);
    }

    /**
     * Fetch all calendar types
     *
     * @return array
     */
    public function findAll(): array
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('elb_calendar_types');
        return $this->findEntities($qb);
    }

}
