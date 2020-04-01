<?php


namespace OCA\ElbCalTypes\Service;


use OCA\Activity\CurrentUser;
use OCA\ElbCalTypes\Db\CalendarTypeGroupFolderMapper;
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

}