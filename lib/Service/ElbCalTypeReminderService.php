<?php


namespace OCA\ElbCalTypes\Service;


use OCA\Activity\CurrentUser;
use OCA\ElbCalTypes\Db\CalendarTypeReminder;
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
    /**
     * @var CurrentUser
     */
    private $currentUser;

    public function __construct(IL10N $l,
                                CalendarTypeReminderMapper $mapper,
                                ElbCalDefRemindersService $defaultReminderService,
                                CurrentUser $currentUser)
    {
        $this->l = $l;
        $this->mapper = $mapper;
        $this->defaultReminderService = $defaultReminderService;
        $this->currentUser = $currentUser;
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
                    'minutes_before_event' => $res['minutes_before_event'],
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

    public function assignRemindersForCalendarTypeID($data)
    {
        $calendarTypeID = $data['caltypeid'];
        $defReminders = $data['reminders'];
        foreach ($defReminders as $key => $defReminderID) {
            $calTypeReminder = new CalendarTypeReminder();
            $calTypeReminder->setFkElbCalType($calendarTypeID);
            $calTypeReminder->setFkElbDefReminder($defReminderID);
            $calTypeReminder->setUserAuthor($this->currentUser->getUID());
            $calTypeReminder->setCreatedAt(date('Y-m-d H:i:s', time()));
            $this->mapper->insert($calTypeReminder);
        }
        return [];
    }

    public function removeReminderForCalendarTypeID($data)
    {
        $linkID = $data['caltyperemid'];
        $calTypeReminder = new CalendarTypeReminder();
        $calTypeReminder->id = $linkID;
        return $this->mapper->delete($calTypeReminder);
    }

    public function getAssignedRemindersForCalTypesIds($calTypesIds)
    {
        $out = [];
        $results = $this->mapper->getAssignedRemindersForCalendarTypes($calTypesIds);
        if (is_array($results) && count($results)) {
            $transForDefReminders = $this->defaultReminderService->getTranslatedTitlesForEachDefaultReminder();
            foreach ($results as $res) {
                $out[$res['cal_type_id']][$res['cal_def_reminder_id']] = [
                    'minutes_before_event' => $res['minutes_before_event'],
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