<?php


namespace OCA\ElbCalTypes\Service;


use OCA\Activity\CurrentUser;
use OCA\ElbCalTypes\Db\CalendarTypeGroupFolder;
use OCA\ElbCalTypes\Db\CalendarTypeGroupFolderMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IL10N;

class ElbCalTypeGroupFolderService
{

    /**
     * @var IDBConnection
     */
    private $db;

    public function __construct(IL10N $l,
                                CalendarTypeGroupFolderMapper $mapper,
                                CurrentUser $currentUser,
                                IDBConnection $db)
    {
        $this->l = $l;
        $this->mapper = $mapper;
        $this->currentUser = $currentUser;
        $this->db = $db;
    }


    public function returnAssignedGroupFoldersForCalendarTypes()
    {
        $results = $this->mapper->findAll();
        if (is_array($results) && count($results)) {
            foreach ($results as $res) {
                $out[$res->getFkElbCalType()][$res->getId()] = [
                    'link_id' => $res->getId(),
                    'cal_type_id' => $res->getFkElbCalType(),
                    'gf_id' => $res->getFkGroupFolder(),
                ];
            }
        }
        return $out;
    }

    public function assignGroupFoldersForCalendarTypeID($data)
    {
        $calendarTypeID = $data['caltypeid'];
        $groupFolders = $data['groupfolders'];
        foreach ($groupFolders as $key => $gfID) {
            $gf = new CalendarTypeGroupFolder();
            $gf->setFkElbCalType($calendarTypeID);
            $gf->setFkGroupFolder($gfID);
            $gf->setUserAuthor($this->currentUser->getUID());
            $gf->setCreatedAt(date('Y-m-d H:i:s', time()));
            $this->mapper->insert($gf);
        }
        return [];
    }

    public function removeGroupFolderForCalendarTypeID($data)
    {
        $linkID = $data['linkid'];
        $calTypeGF = new CalendarTypeGroupFolder();
        $calTypeGF->id = $linkID;
        return $this->mapper->delete($calTypeGF);
    }

    public function getCalendarTypesAssignedForGroupFoldersIDs(array $gfIDs)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('gct.id as link_id')
            ->from('elb_gf_cal_types', 'gct')
            ->where($qb->expr()->in('gct.fk_group_folder', $qb->createNamedParameter($gfIDs, IQueryBuilder::PARAM_INT_ARRAY)));
        return $qb->execute()->fetchAll();
    }

}