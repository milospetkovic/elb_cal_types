<?php


namespace OCA\ElbCalTypes\Service;


use OCA\Activity\CurrentUser;
use OCA\ElbCalTypes\Db\GroupFoldersMapper;

class ElbGroupFoldersService
{

    /**
     * @var GroupFoldersMapper
     */
    private $mapper;
    /**
     * @var CurrentUser
     */
    private $currentUser;

    public function __construct(GroupFoldersMapper $mapper,
                                CurrentUser $currentUser)
    {
        $this->mapper = $mapper;
        $this->currentUser = $currentUser;
    }

    /**
     * Fetch all group folders
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->mapper->findAll();
    }

}