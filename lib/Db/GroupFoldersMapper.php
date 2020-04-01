<?php


namespace OCA\ElbCalTypes\Db;


use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class GroupFoldersMapper extends QBMapper
{
    private $table_name = 'group_folders';

    /**
     * GroupFoldersMapper constructor.
     * @param IDBConnection $db
     */
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, $this->table_name, GroupFolders::class);
    }

    /**
     * Fetch all group folders
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