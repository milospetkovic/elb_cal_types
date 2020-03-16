<?php
namespace OCA\ElbCalTypes\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class CalendarTypesMapper extends QBMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'elb_calendar_types', CalendarTypes::class);
    }

    /**
     * @param int $id
     * @param string $userId
     * @return Entity|CalendarTypes
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     * @throws DoesNotExistException
     */
    public function find(int $id): CalendarTypes {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('elb_calendar_types')
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        return $this->findEntity($qb);
    }

    /**
     * @param string $userId
     * @return array
     */
    public function findAll(): array {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('elb_calendar_types');
        return $this->findEntities($qb);
    }

}
