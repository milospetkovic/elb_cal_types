<?php


namespace OCA\ElbCalTypes\Service;


use OCA\Activity\CurrentUser;
use OCA\ElbCalTypes\Db\CalendarTypeEvent;
use OCA\ElbCalTypes\Db\CalendarTypeEventMapper;
use OCA\ElbCalTypes\Db\CalendarTypeEventReminder;
use OCA\ElbCalTypes\Db\CalendarTypeEventReminderMapper;
use OCA\ElbCalTypes\Db\CalendarTypeEventUser;
use OCA\ElbCalTypes\Db\CalendarTypeEventUserMapper;
use OCA\ElbCalTypes\Manager\CalendarManager;

class ElbCalTypeEventService
{
    /**
     * @var CalendarTypeEventMapper
     */
    private $calendarTypeEventMapper;
    /**
     * @var CalendarTypeEvent
     */
    private $calendarTypeEvent;
    /**
     * @var CurrentUser
     */
    private $currentUser;
    /**
     * @var CalendarTypeEventReminderMapper
     */
    private $calendarTypeEventReminderMapper;
    /**
     * @var CalendarTypeEventReminder
     */
    private $calendarTypeEventReminder;
    /**
     * @var CalendarTypeEventUserMapper
     */
    private $calendarTypeEventUserMapper;
    /**
     * @var CalendarTypeEventUser
     */
    private $calendarTypeEventUser;
    /**
     * @var ElbCalEventService
     */
    private $calEventService;
    /**
     * @var CalendarManager
     */
    private $calendarManager;

    public function __construct(CalendarTypeEventMapper $calendarTypeEventMapper,
                                CalendarTypeEvent $calendarTypeEvent,
                                CalendarTypeEventReminderMapper $calendarTypeEventReminderMapper,
                                CalendarTypeEventReminder $calendarTypeEventReminder,
                                CalendarTypeEventUserMapper $calendarTypeEventUserMapper,
                                CalendarTypeEventUser $calendarTypeEventUser,
                                CurrentUser $currentUser,
                                ElbCalEventService $calEventService,
                                CalendarManager $calendarManager)
    {
        $this->calendarTypeEventMapper = $calendarTypeEventMapper;
        $this->calendarTypeEvent = $calendarTypeEvent;
        $this->calendarTypeEventReminderMapper = $calendarTypeEventReminderMapper;
        $this->calendarTypeEventReminder = $calendarTypeEventReminder;
        $this->currentUser = $currentUser;
        $this->calendarTypeEventUserMapper = $calendarTypeEventUserMapper;
        $this->calendarTypeEventUser = $calendarTypeEventUser;
        $this->calEventService = $calEventService;
        $this->calendarManager = $calendarManager;
    }

    // @TODO implement validation and empty values as null...
    public function storeCalendarTypeEvent($data)
    {
        // $calTypeId = $data['caltypeid'];
        $calTypeLinkId = $data['caltypelinkid'];
        (empty($data['eventname']) ? $eventName=null : $eventName=trim($data['eventname']));
        $eventDescription = $data['eventdesc'];
        $eventDateTime = $data['eventdatetime'];
        $eventAssignedReminders = $data['reminders'];
        $eventAssignedUsers = $data['assignedusers'];

        $calTypeEvent = new $this->calendarTypeEvent;
        $calTypeEvent->setFkGfCalType($calTypeLinkId);
        $calTypeEvent->setUserAuthor($this->currentUser->getUID());
        $calTypeEvent->setCreatedAt(date('Y-m-d H:i:s', time()));
        $calTypeEvent->setTitle($eventName);
        $calTypeEvent->setDescription($eventDescription);
        $calTypeEvent->setEventDatetime($eventDateTime);
        $this->calendarTypeEventMapper->insert($calTypeEvent);

        if ($calTypeEvent->id > 0) {

            $calTypeEventID = $calTypeEvent->id;

            if (is_array($eventAssignedReminders) && count($eventAssignedReminders)) {
                foreach ($eventAssignedReminders as $aRem) {
                    $calTypeEventReminder = new $this->calendarTypeEventReminder;
                    $calTypeEventReminder->setFkCalTypeEvent($calTypeEventID);
                    $calTypeEventReminder->setFkCalDefReminder($aRem['id']);
                    $this->calendarTypeEventReminderMapper->insert($calTypeEventReminder);
                }
            }

            if (is_array($eventAssignedUsers) && count($eventAssignedUsers)) {
                foreach ($eventAssignedUsers as $aUserData) {
                    $calTypeEventUser = new $this->calendarTypeEventUser;
                    $calTypeEventUser->setFkCalTypeEvent($calTypeEventID);
                    $calTypeEventUser->setFkUser($aUserData['userID']);
                    $this->calendarTypeEventUserMapper->insert($calTypeEventUser);
                }
            }
        }
    }

    public function getCalendarTypeEvents($data)
    {
        $ret = [];
        $res = $this->calendarTypeEventMapper->fetchCalendarTypeEventsByLinkIDWithGroupFolder($data['caltypelinkid']);
        if (is_array($res) && count($res)) {
            foreach($res as $ind => $arr) {
                if (!array_key_exists($arr['cal_type_event_id'], $ret)) {
                    $ret[$arr['cal_type_event_id']] = [
                        'link_id' => $arr['cal_type_event_id'],
                        'event_title' => $arr['event_title'],
                        'event_description' => $arr['event_description'],
                        'event_datetime' => $arr['event_datetime'],
                        'event_executed' => $arr['event_executed'],
                        'event_title' => $arr['event_title'],
                        'event_assigned_users' => [],
                        'event_assigned_reminders' => []
                    ];
                }

                if (!in_array($arr['assigned_user_id'], $ret[$arr['cal_type_event_id']]['event_assigned_users'])) {
                    $ret[$arr['cal_type_event_id']]['event_assigned_users'][] = $arr['assigned_user_id'];
                }

                if (!array_key_exists($arr['event_def_reminder_id'], $ret[$arr['cal_type_event_id']]['event_assigned_reminders'])) {
                    $ret[$arr['cal_type_event_id']]['event_assigned_reminders'][$arr['event_def_reminder_id']] = ['def_reminder_title' => $arr['def_reminder_title']];
                }
            }
        }
        if (!count($ret)) {
            return false;
        }
        return $ret;
    }

    public function deleteCalendarTypeEvent($data)
    {
        $linkID = $data['linkid'];
        $calTypeEvent = new $this->calendarTypeEvent;
        $calTypeEvent->id = $linkID;
        return $this->calendarTypeEventMapper->delete($calTypeEvent);
    }

    public function saveCalendarEventForUsersForCalendarTypeEventID($params)
    {
        $ret = false;
        $data = $this->getCalendarTypeEventDataByCalTypeEventID($params['caltypeeventid']);
        if (is_array($data) && count($data)) {
            $ret = $this->calendarManager->createCalendarWithEventAndRemindersForUsersForCalTypeEvent($data[$params['caltypeeventid']]);
        }
        return $ret;
    }

    private function getCalendarTypeEventDataByCalTypeEventID($id)
    {
        $ret = [];
        $res = $this->calendarTypeEventMapper->fetchCalendarTypeEventDataByCalTypeEventID($id);
        if (is_array($res) && count($res)) {
            foreach($res as $ind => $arr) {
                if (!array_key_exists($arr['cal_type_event_id'], $ret)) {
                    $ret[$arr['cal_type_event_id']] = [
                        'event_id' => $arr['cal_type_event_id'],
                        'event_title' => $arr['event_title'],
                        'event_description' => $arr['event_description'],
                        'event_datetime' => $arr['event_datetime'],
                        'event_executed' => $arr['event_executed'],
                        'event_title' => $arr['event_title'],
                        'event_cal_type_title' => $arr['cal_type_title'],
                        'event_cal_type_description' => $arr['cal_type_description'],
                        'event_cal_type_slug' => $arr['cal_type_slug'],
                        'event_assigned_users' => [],
                        'event_assigned_reminders' => []
                    ];
                }

                if (!in_array($arr['assigned_user_id'], $ret[$arr['cal_type_event_id']]['event_assigned_users'])) {
                    $ret[$arr['cal_type_event_id']]['event_assigned_users'][] = $arr['assigned_user_id'];
                }

                if (!array_key_exists($arr['event_def_reminder_id'], $ret[$arr['cal_type_event_id']]['event_assigned_reminders'])) {
                    $ret[$arr['cal_type_event_id']]['event_assigned_reminders'][$arr['event_def_reminder_id']] = ['def_reminder_title' => $arr['def_reminder_title'],
                                                                                                                  'def_reminder_minutes_before_event' => $arr['def_reminder_minutes_before_event']];
                }
            }
        }
        if (!count($ret)) {
            return false;
        }
        return $ret;
    }

}