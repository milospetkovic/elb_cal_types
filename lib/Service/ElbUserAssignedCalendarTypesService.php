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
     * @var ElbCalTypeGroupFolderService
     */
    private $elbCalTypeGroupFolderService;

    /**
     * ElbUserAssignedCalendarTypesService constructor.
     * @param IL10N $l
     * @param IDBConnection $db
     * @param CurrentUser $currentUser
     * @param ElbGroupFolderUserService $elbGroupFolderUserService
     * @param ElbCalTypeGroupFolderService $elbCalTypeGroupFolderService
     */
    public function __construct(IL10N $l,
                                IDBConnection $db,
                                CurrentUser $currentUser,
                                ElbGroupFolderUserService $elbGroupFolderUserService,
                                ElbCalTypeGroupFolderService $elbCalTypeGroupFolderService)
    {
        $this->l = $l;
        $this->db = $db;
        $this->currentUser = $currentUser;
        $this->elbGroupFolderUserService = $elbGroupFolderUserService;
        $this->elbCalTypeGroupFolderService = $elbCalTypeGroupFolderService;
    }

    public function getAssignedCalendarTypes()
    {
        $ret = [];
        $gfIDs =  $this->elbGroupFolderUserService->getGroupFoldersIdsLinkedWithUser();

        if (is_array($gfIDs) && count($gfIDs)) {
            $ret = $this->elbCalTypeGroupFolderService->getCalendarTypesAssignedForGroupFoldersIDs($gfIDs);
        }
        return $ret;
    }

}