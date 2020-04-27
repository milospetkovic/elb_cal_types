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
use OCP\IDBConnection;
use OCP\IL10N;
use OCA\ElbCalTypes\Util\DateTimeUtility;

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
     * @var CalendarManager
     */
    private $calendarManager;
    /**
     * @var IL10N
     */
    private $l;
    /**
     * @var IDBConnection
     */
    private $connection;

    public function __construct(CalendarTypeEventMapper $calendarTypeEventMapper,
                                CalendarTypeEvent $calendarTypeEvent,
                                CalendarTypeEventReminderMapper $calendarTypeEventReminderMapper,
                                CalendarTypeEventReminder $calendarTypeEventReminder,
                                CalendarTypeEventUserMapper $calendarTypeEventUserMapper,
                                CalendarTypeEventUser $calendarTypeEventUser,
                                CurrentUser $currentUser,
                                CalendarManager $calendarManager,
                                IL10N $l,
                                IDBConnection $connection)
    {
        $this->calendarTypeEventMapper = $calendarTypeEventMapper;
        $this->calendarTypeEvent = $calendarTypeEvent;
        $this->calendarTypeEventReminderMapper = $calendarTypeEventReminderMapper;
        $this->calendarTypeEventReminder = $calendarTypeEventReminder;
        $this->currentUser = $currentUser;
        $this->calendarTypeEventUserMapper = $calendarTypeEventUserMapper;
        $this->calendarTypeEventUser = $calendarTypeEventUser;
        $this->calendarManager = $calendarManager;
        $this->l = $l;
        $this->connection = $connection;
    }

    public function storeCalendarTypeEvent($data)
    {
        $error = 0;
        $error_msg = [];

        $validateRes = $this->validateCalTypeEventData($data);
        if ($validateRes['error']) {
            $error++;
            $error_msg = $validateRes['error_msg'];
        }

        // start transaction
        $this->connection->beginTransaction();

        if (!$error) {

            $calTypeLinkId = $data['caltypelinkid'];
            (empty($data['eventname']) ? $eventName = null : $eventName = trim($data['eventname']));
            $eventDescription = $data['eventdesc'];

            $tsEventDateTime = strtotime($data['eventdatetime']);
            $eventDateTime = date('Y-m-d\TH:i:s.000Z', $tsEventDateTime);

            $eventEndDateTime = null;
            if ($data['eventenddatetime']) {
                $tsEventEndDateTime = strtotime($data['eventenddatetime']);
                $eventEndDateTime = date('Y-m-d\TH:i:s.000Z', $tsEventEndDateTime);
            }

            $eventAssignedReminders = $data['reminders'];
            $eventAssignedUsers = $data['assignedusers'];

            $calTypeEvent = new $this->calendarTypeEvent;
            $calTypeEvent->setFkGfCalType($calTypeLinkId);
            $calTypeEvent->setUserAuthor($this->currentUser->getUID());
            $calTypeEvent->setCreatedAt(date('Y-m-d H:i:s', time()));
            $calTypeEvent->setTitle($eventName);
            $calTypeEvent->setDescription($eventDescription);
            $calTypeEvent->setEventDatetime($eventDateTime);
            $calTypeEvent->setEventEndDatetime($eventEndDateTime);
            $this->calendarTypeEventMapper->insert($calTypeEvent);

            if ($calTypeEvent->id > 0) {

                $calTypeEventID = $calTypeEvent->id;

                if (is_array($eventAssignedReminders) && count($eventAssignedReminders)) {
                    foreach ($eventAssignedReminders as $aRem) {
                        $calTypeEventReminder = new $this->calendarTypeEventReminder;
                        $calTypeEventReminder->setFkCalTypeEvent($calTypeEventID);
                        $calTypeEventReminder->setFkCalDefReminder($aRem['id']);
                        $this->calendarTypeEventReminderMapper->insert($calTypeEventReminder);
                        if (!($calTypeEvent->id > 0)) {
                            $error++;
                            $error_msg[] = $this->l->t('Error saving reminder for calendar type event');
                            break;
                        }
                    }
                }

                if (!$error) {
                    if (is_array($eventAssignedUsers) && count($eventAssignedUsers)) {
                        foreach ($eventAssignedUsers as $aUserData) {
                            $calTypeEventUser = new $this->calendarTypeEventUser;
                            $calTypeEventUser->setFkCalTypeEvent($calTypeEventID);
                            $calTypeEventUser->setFkUser($aUserData['userID']);
                            $this->calendarTypeEventUserMapper->insert($calTypeEventUser);
                            if (!($calTypeEventUser->id > 0)) {
                                $error++;
                                $error_msg[] = $this->l->t('Error assigning user for calendar type event');
                                break;
                            }
                        }
                    }
                }
            } else {
                $error++;
                $error_msg[] = $this->l->t('Error saving event for calendar type');
            }
        }

        (!$error) ? $this->connection->commit() : $this->connection->rollBack();

        return [
            'error' => $error,
            'error_msg' => count($error_msg) ? implode(". ", $error_msg) : ''
        ];
    }

    private function validateCalTypeEventData($data)
    {
        $ret = ['error' => 0, 'error_msg' => []];

        if (empty($data['caltypelinkid'])) {
            $ret['error'] += 1;
            $ret['error_msg'][]= $this->l->t('Link with calendar type is required');
        }
        if (empty($data['eventname'])) {
            $ret['error'] += 1;
            $ret['error_msg'][]= $this->l->t('Event name is required');
        }

        if (!strtotime($data['eventdatetime'])) {
            $ret['error'] += 1;
            $ret['error_msg'][]= $this->l->t('Event date and time is required');
        }

        if (!(is_array($data['reminders']) && count($data['reminders']))) {
            $ret['error'] += 1;
            $ret['error_msg'][]= $this->l->t('At least one reminder for calendar event is required');
        }

        if (!(is_array($data['assignedusers']) && count($data['assignedusers']))) {
            $ret['error'] += 1;
            $ret['error_msg'][]= $this->l->t('At least one assigned user for calendar event is required');
        }

        return $ret;
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
                        'event_datetime' => ($arr['event_datetime'] ? DateTimeUtility::convertDateTimeToUserFriendlyDateTime($arr['event_datetime']) : ''),
                        'event_end_datetime' => ($arr['event_end_datetime'] ? DateTimeUtility::convertDateTimeToUserFriendlyDateTime($arr['event_end_datetime']) : ''),
                        'event_executed' => $arr['event_executed'],
                        'event_assigned_users' => [],
                        'event_assigned_reminders' => []
                    ];
                }
                //($arr['event_end_datetime'] ? DateTimeUtility::convertDateTimeToUserFriendlyDateTime($arr['event_end_datetime']) : $this->l->t('No'))

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
                        'event_end_datetime' => $arr['event_end_datetime'],
                        'event_executed' => $arr['event_executed'],
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