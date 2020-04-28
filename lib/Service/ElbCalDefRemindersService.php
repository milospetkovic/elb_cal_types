<?php


namespace OCA\ElbCalTypes\Service;


use OCA\ElbCalTypes\Db\CalendarDefaultRemindersMapper;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IL10N;

class ElbCalDefRemindersService
{
    /**
     * @var IL10N
     */
    private $l;

    /**
     * @var CalendarDefaultRemindersMapper
     */
    private $mapper;

    /**
     * ElbCalDefRemindersService constructor.
     * @param IL10N $l
     * @param CalendarDefaultRemindersMapper $mapper
     */
    public function __construct(IL10N $l,
                                CalendarDefaultRemindersMapper $mapper)
    {
        $this->l = $l;
        $this->mapper = $mapper;
    }

    public function returnDefaultDefinedReminders()
    {
        return [
            0 => $this->l->t('at the event\'s start'),
            1 => $this->l->t('1 minute before the event starts'),
            2 => $this->l->t('2 minutes before the event starts'),
            5 => $this->l->t('5 minutes before the event starts'),
            10 => $this->l->t('10 minutes before the event starts'),
            15 => $this->l->t('15 minutes before the event starts'),
            20 => $this->l->t('20 minutes before the event starts'),
            30 => $this->l->t('30 minutes before the event starts'),
            45 => $this->l->t('45 minutes before the event starts'),
            60 => $this->l->t('1 hour before the event starts'),
            120 => $this->l->t('2 hours before the event starts'),
            180 => $this->l->t('3 hours before the event starts'),
            360 => $this->l->t('6 hours before the event starts'),
            1440 => $this->l->t('1 day before the event starts'),
            2880 => $this->l->t('2 days before the event starts'),
            4320 => $this->l->t('3 days before the event starts'),
            7200 => $this->l->t('5 days before the event starts'),
            10080 => $this->l->t('1 week before the event starts'),
            20160 => $this->l->t('2 weeks before the event starts'),
            43200 => $this->l->t('1 month before the event starts'),
            86400 => $this->l->t('2 months before the event starts'),
            129600 => $this->l->t('3 months before the event starts'),
            259200 => $this->l->t('6 months before the event starts'),
            525600 => $this->l->t('1 year before the event starts'),
        ];
    }

    public function returnCalendarReminderSyntaxForDefaultReminders()
    {
        return [
            0 => 'PT0S',
            1 => '-PT1M',
            2 => '-PT2M',
            5 => '-PT5M',
            10 => '-PT10M',
            15 => '-PT15M',
            20 => '-PT20M',
            30 => '-PT30M',
            45 => '-PT40M',
            60 => '-PT1H',
            120 => '-PT2H',
            180 => '-PT3H',
            360 => '-PT6H',
            1440 => '-P1D',
            2880 => '-P2D',
            4320 => '-P3D',
            7200 => '-P5D',
            10080 => '-P7D',
            20160 => '-P2W',
            43200 => '-P30D',
            86400 => '-P60D',
            129600 => '-P90D',
            259200 => '-P180D',
            525600 => '-P365D',
        ];
    }

    /**
     * Fetch all default calendar reminders
     *
     * @param bool $getEntities
     * @return array
     */
    public function findAll($getEntities=true): array
    {
        $getDataArray = $ret = $this->mapper->findAll($getEntities);
        if ($getEntities) {
            if (is_array($getDataArray) && count($getDataArray)) {
                $ret = [];
                foreach ($getDataArray as $entity) {
                    $entity->setTitle($this->l->t($entity->getTitle()));
                    $ret[] = $entity;
                }
            }
        }
        return $ret;
    }

    public function getTranslatedTitlesForEachDefaultReminder()
    {
        $out = [];
        $rows = $this->findAll(false);
        if (count($rows)) {
            foreach ($rows as $ind => $obj) {
                $out[$obj['id']] = $this->l->t($obj['title']);
            }
        }
        return $out;
    }

}