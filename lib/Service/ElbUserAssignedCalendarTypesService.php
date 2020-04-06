<?php


namespace OCA\ElbCalTypes\Service;


use OCA\Activity\CurrentUser;
use OCP\IDBConnection;
use OCP\IL10N;

class ElbUserAssignedCalendarTypesService
{
    /**
     * @var IL10N
     */
    private $l;
    /**
     * @var IDBConnection
     */
    private $db;
    /**
     * @var CurrentUser
     */
    private $currentUser;
    /**
     * @var ElbGroupFolderUserService
     */
    private $elbGroupFolderUserService;

    /**
     * ElbUserAssignedCalendarTypesService constructor.
     * @param IL10N $l
     * @param IDBConnection $db
     * @param CurrentUser $currentUser
     * @param ElbGroupFolderUserService $elbGroupFolderUserService
     */
    public function __construct(IL10N $l,
                                IDBConnection $db,
                                CurrentUser $currentUser,
                                ElbGroupFolderUserService $elbGroupFolderUserService)
    {
        $this->l = $l;
        $this->db = $db;
        $this->currentUser = $currentUser;
        $this->elbGroupFolderUserService = $elbGroupFolderUserService;
    }

    public function getAssignedCalendarTypes()
    {
        return $this->elbGroupFolderUserService->getGroupFoldersIdsLinkedWithUser();
    }

}