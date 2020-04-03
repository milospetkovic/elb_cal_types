<?php


namespace OCA\ElbCalTypes\Service;


use OCA\Activity\CurrentUser;
use OCP\IDBConnection;
use OCP\IL10N;

class ElbGroupFolderUserService
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

    public function __construct(IL10N $l,
                                IDBConnection $db,
                                CurrentUser $currentUser)
    {
        $this->l = $l;
        $this->db = $db;
        $this->currentUser = $currentUser;
    }

    public function isCurrentUserAdminForGroupFolder()
    {
        $isGroupFolderAdmin = $this->checkUpIfUserByItsUserIDIsAssignedToManageGroupFolder();
        return ['isGroupFolderAdmin' => $isGroupFolderAdmin];
    }

    private function checkUpIfUserByItsUserIDIsAssignedToManageGroupFolder()
    {
        $res = false;

        $stmt = $this->db->prepare('SELECT 1 FROM `*PREFIX*group_folders_manage` where `mapping_type`= "user" and `mapping_id`="'.$this->currentUser->getUID().'"' );
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $res = true;
        }
        $stmt->closeCursor();

        return $res;
    }

}