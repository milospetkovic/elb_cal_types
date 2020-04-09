<?php


namespace OCA\ElbCalTypes\Service;


use OCA\Activity\CurrentUser;
use OCP\DB\QueryBuilder\IQueryBuilder;
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
        // check if user is group folder admin by it's id
        $isGroupFolderAdmin = $this->checkUpIfUserByItsUserIDIsAssignedToManageGroupFolder();
        if (!$isGroupFolderAdmin) {
            // check up if user belongs to a user group which is assigned as group folder admin
            $isGroupFolderAdmin = $this->checkUpIfUserByUserGroupIsAssignedToManageGroupFolder();
        }
        return ['isGroupFolderAdmin' => $isGroupFolderAdmin];
    }

    private function checkUpIfUserByUserGroupIsAssignedToManageGroupFolder()
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

    private function checkUpIfUserByItsUserIDIsAssignedToManageGroupFolder()
    {
        $res = false;

        $stmt = $this->db->prepare('SELECT 1 FROM `*PREFIX*group_folders_manage` as `gfm` left join `*PREFIX*group_user` as `gu` on `gu`.`gid`=`gfm`.`mapping_id` where `gfm`.`mapping_type`= "group" and `gu`.`uid`="'.$this->currentUser->getUID().'"' );
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $res = true;
        }
        $stmt->closeCursor();

        return $res;
    }

    /**
     * Get group folders ids which are assigned to the user by user ID's
     *
     * @return array
     */
    private function getGroupFolderIdsAssignedForUserByUserId()
    {
        $ret = [];
        $qb = $this->db->getQueryBuilder();
        $qb->select('gfm.folder_id')
            ->from('group_folders_manage', 'gfm')
            ->where($qb->expr()->eq('gfm.mapping_id', $qb->createNamedParameter($this->currentUser->getUID())))
            ->andWhere('gfm.mapping_type="user"')
            ->groupBy('gfm.folder_id');
        $res = $qb->execute()->fetchAll();

        if (is_array($res) && count($res)) {
            foreach ($res as $rowData) {
                $ret[] = $rowData['folder_id'];
            }
        }
        return $ret;
    }

    /**
     * Get group folders ids which are assigned to the user by user's group
     *
     * @return array
     */
    private function getGroupFolderIdsAssignedForUserByUserGroup()
    {
        $ret = [];
        $qb = $this->db->getQueryBuilder();
        $qb->select('gfm.folder_id')
            ->from('group_folders_manage', 'gfm')
            ->where($qb->expr()->eq('gu.uid', $qb->createNamedParameter($this->currentUser->getUID())))
            ->andWhere('gfm.mapping_type="group"')
            ->leftJoin('gfm', 'group_user', 'gu', $qb->expr()->eq('gu.gid', 'gfm.mapping_id'))
            ->groupBy('gfm.folder_id');
        $res = $qb->execute()->fetchAll();

        if (is_array($res) && count($res)) {
            foreach ($res as $rowData) {
                $ret[] = $rowData['folder_id'];
            }
        }
        return $ret;
    }

    /**
     * Get all group folders ids which are available for the user to manage
     *
     * @return array
     */
    public function getGroupFoldersIdsLinkedWithUser()
    {
        $gfIdsForUserId = $this->getGroupFolderIdsAssignedForUserByUserId();
        $gfIdsForUserGroup = $this->getGroupFolderIdsAssignedForUserByUserGroup();
        $retArr = array_unique(array_merge($gfIdsForUserId, $gfIdsForUserGroup));
        return $retArr;
    }


    public function getUsersAssignedToGroupFolders(array $gfIDs)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('gfg.folder_id', 'g.gid as user_group_id', 'gu.uid as user_id')
            ->from('group_folders_groups', 'gfg')
            ->leftJoin('gfg', 'groups', 'g', $qb->expr()->eq('g.gid', 'gfg.group_id'))
            ->leftJoin('g', 'group_user', 'gu', $qb->expr()->eq('gu.gid', 'g.gid'))
            ->where($qb->expr()->in('gfg.folder_id', $qb->createNamedParameter($gfIDs, IQueryBuilder::PARAM_STR_ARRAY)));
        $res = $qb->execute()->fetchAll();
        return $res;
    }


}