<?php


namespace OCA\ElbCalTypes\Service;


use OCA\Activity\CurrentUser;
use OCA\ElbCalTypes\Db\CalendarTypeGroupFolder;
use OCA\ElbCalTypes\Db\CalendarTypeGroupFolderMapper;
use OCA\ElbCalTypes\Db\GroupFolders;
use OCP\IL10N;

class ElbCalTypeGroupFolderService
{

    public function __construct(IL10N $l,
                                CalendarTypeGroupFolderMapper $mapper,
                                CurrentUser $currentUser)
    {
        $this->l = $l;
        $this->mapper = $mapper;
        $this->currentUser = $currentUser;
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

}