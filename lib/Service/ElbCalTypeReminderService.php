<?php


namespace OCA\ElbCalTypes\Service;


use OCA\ElbCalTypes\Db\CalendarTypeReminderMapper;
use OCP\IL10N;

class ElbCalTypeReminderService
{

    /**
     * @var IL10N
     */
    private $l;
    /**
     * @var CalendarTypeReminderMapper
     */
    private $mapper;
    /**
     * @var ElbCalDefRemindersService
     */
    private $defaultReminderService;

    public function __construct(IL10N $l,
                                CalendarTypeReminderMapper $mapper,
                                ElbCalDefRemindersService $defaultReminderService)
    {
        $this->l = $l;
        $this->mapper = $mapper;
        $this->defaultReminderService = $defaultReminderService;
    }

    /**
     * Fetch all default calendar reminders
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->mapper->findAll();
    }

    public function returnAssignedRemindersForCalendarTypes()
    {
        $out = [];
        $results = $this->mapper->getAssignedRemindersForCalendarTypes();
        if (is_array($results) && count($results)) {
            $transForDefReminders = $this->defaultReminderService->getTranslatedTitlesForEachDefaultReminder();
            foreach ($results as $res) {
                $out[$res['cal_type_id']][$res['link_id']] = [
                    'calendar_type_id' => $res['cal_type_id'],
                    'link_id' => $res['link_id'],
                    'cal_def_reminder_id' => $res['cal_def_reminder_id'],
                    'cal_def_reminder_title' => $res['cal_def_reminder_id'],
                    'cal_def_reminder_title_trans' => $transForDefReminders[$res['cal_def_reminder_id']],
                ];
            }
        }
        return $out;
    }

}