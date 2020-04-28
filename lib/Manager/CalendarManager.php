<?php

namespace OCA\ElbCalTypes\Manager;

use OCA\DAV\CalDAV\CalDavBackend;
use OCA\ElbCalTypes\Db\CalendarTypeEventMapper;
use OCA\ElbCalTypes\Service\ElbCalDefRemindersService;
use OCP\IDBConnection;
use OCP\IL10N;
use Sabre\DAV\Exception;
use Sabre\DAV\UUIDUtil;
use OCA\DAV\CalDAV\Reminder\Backend as CalendarReminderManager;
use OCA\DAV\CalDAV\Reminder\ReminderService;

class CalendarManager
{
    const CALENDAR_PRINCIPAL_URI_PREFIX = 'principals/users/';

    /**
     * @var CalDavBackend
     */
    private $calDavBackend;

    private $checkedIfCalendarExistsForUser = [];

    /**
     * @var IDBConnection
     */
    private $connection;

    /**
     * @var IL10N
     */
    private $l;
    /**
     * @var CalendarReminderManager
     */
    private $calendarReminderManager;
    /**
     * @var ElbCalDefRemindersService
     */
    private $elbCalDefRemindersService;
    /**
     * @var CalendarTypeEventMapper
     */
    private $calendarTypeEventMapper;

    /**
     * CalendarManager constructor.
     * @param CalDavBackend $calDavBackend
     * @param IL10N $l
     * @param IDBConnection $connection
     * @param CalendarReminderManager $calendarReminderManager
     * @param ElbCalDefRemindersService $calDefRemindersService
     */
    public function __construct(CalDavBackend $calDavBackend,
                                IL10N $l,
                                IDBConnection $connection,
                                CalendarReminderManager $calendarReminderManager,
                                ElbCalDefRemindersService $elbCalDefRemindersService,
                                CalendarTypeEventMapper $calendarTypeEventMapper)
    {
        $this->calDavBackend = $calDavBackend;
        $this->l = $l;
        $this->connection = $connection;
        $this->calendarReminderManager = $calendarReminderManager;
        $this->elbCalDefRemindersService = $elbCalDefRemindersService;
        $this->calendarTypeEventMapper = $calendarTypeEventMapper;
    }

    public function createCalendarEventWithReminders($calendarID, $calTypeEventTitle, $calTypeEventDescription, $calTypeEventDatetime, $calTypeEventEndDatetime, array $eventReminders)
    {
        // uuid for .ics
        $calDataUri = strtoupper(UUIDUtil::getUUID()) . '.ics';

        // the datetime when calendar event object is created
        $createdDateTime = date('Ymd\THis\Z');

        // uuid for calendar object itself
        $calObjectUUID = strtolower(UUIDUtil::getUUID());

        // the name for calendar event
        $eventSummary = $calTypeEventTitle;

        // timestamp of start date/time for calendar type event
        $tsCalTypeEventDatetime = strtotime($calTypeEventDatetime);

        // the start date time of calendar event (end datatime is equal as start date time in case when end date time is not provided)
        $eventStartDatetime = $eventEndDatetime = date('Ymd\THis\Z', $tsCalTypeEventDatetime);

        // set end date/time of event if it's provided
        if ($calTypeEventEndDatetime) {
            $eventEndDatetime = date('Ymd\THis\Z', strtotime($calTypeEventEndDatetime));
        }

        $tsOffsetFrom = '+0000';
        $tsOffsetTo = '+0000';

        $timeZone = 'Europe/Belgrade';

        // populate start calendar event with data
        $calData = "BEGIN:VCALENDAR\r\n
PRODID:-//IDN nextcloud.com//Calendar app 2.0.1//EN\r\n
CALSCALE:GREGORIAN\r\n
VERSION:2.0\r\n
BEGIN:VEVENT\r\n
CREATED:$createdDateTime\r\n
DTSTAMP:$createdDateTime\r\n
LAST-MODIFIED:$createdDateTime\r\n
SEQUENCE:2\r\n
UID:$calObjectUUID\r\n
DTSTART;TZID=$timeZone:$eventStartDatetime\r\n
DTEND;TZID=$timeZone:$eventEndDatetime\r\n
DTSTAMP;VALUE=DATE-TIME:$createdDateTime\r\n
SUMMARY:$eventSummary\r\n";

if (strlen($calTypeEventDescription)) {
    $trimCalTypeEventDescription = trim($calTypeEventDescription);
    $calData.= "DESCRIPTION:$trimCalTypeEventDescription\r\n";
}

if (count($eventReminders)) {
    $arrRemSyntax = $this->elbCalDefRemindersService->returnCalendarReminderSyntaxForDefaultReminders();
    foreach ($eventReminders as $reminder) {
        $calObjRemSyntax = $arrRemSyntax[$reminder['def_reminder_minutes_before_event']];
        $calData.= "BEGIN:VALARM\r\n
ACTION:DISPLAY\r\n
TRIGGER;RELATED=START:$calObjRemSyntax\r\n
END:VALARM\r\n";
    }
}

$calData.="END:VEVENT\r\n";

$calData.="BEGIN:VTIMEZONE\r\n
TZID:$timeZone\r\n
BEGIN:DAYLIGHT\r\n
TZOFFSETFROM:+0100\r\n
TZOFFSETTO:+0200\r\n
TZNAME:CEST\r\n
DTSTART:19700329T020000\r\n
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU\r\n
END:DAYLIGHT\r\n
BEGIN:STANDARD\r\n
TZOFFSETFROM:$tsOffsetFrom\r\n
TZOFFSETTO:$tsOffsetTo\r\n
TZNAME:CET\r\n
DTSTART:19701025T030000\r\n
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU\r\n
END:STANDARD\r\n
END:VTIMEZONE\r\n";

$calData.="END:VCALENDAR";

        $calendarType = CalDavBackend::CALENDAR_TYPE_CALENDAR;

        // call method which executes creating calendar event(s)
        $response = $this->calDavBackend->createCalendarObject($calendarID, $calDataUri, $calData, $calendarType);

        if (!strlen($response)) {
            return false; // error during saving cal. event
        }

        if (count($eventReminders)) {
            // fetch newly created calendar event
            $event = $this->getCalendarEventObject($calendarID, $calDataUri, $calendarType);
            $eventID = $event['id'];
            $eventUID = $event['uid'];
            foreach ($eventReminders as $reminder) {
                $res = $this->calendarReminderManager->insertReminder(
                    $calendarID,
                    $eventID,
                    $eventUID,
                    false,
                    0,
                    false,
                    'event_hash',
                    'alarm_hash',
                    ReminderService::REMINDER_TYPE_DISPLAY,
                    true,
                    ($tsCalTypeEventDatetime - ($reminder['def_reminder_minutes_before_event'] * 60)),
                    false
                );
                if (!($res > 0)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function createCalendarForUserIfCalendarNotExists($calendarForUser, $calendarSlug, $calendarTitle)
    {
        if (!array_key_exists($calendarForUser, $this->checkedIfCalendarExistsForUser)) {

            // fetch existing calendars for user
            $existingCalendarsForUser = $this->calDavBackend->getCalendarsForUser($this->returnCalendarPrincipalUriForUser($calendarForUser));

            // check up if calendar already exists (return id of calendar in that case)
            if (is_array($existingCalendarsForUser) && count($existingCalendarsForUser)) {
                foreach ($existingCalendarsForUser as $ind => $arr) {
                    if ($arr['uri'] == $calendarSlug) {
                        $this->checkedIfCalendarExistsForUser[$calendarForUser] = $arr['id'];
                        return $arr['id'];
                    }
                }
            }

            // calendar doesn't exist -> create a calendar for the user
            try {
                $newCalendarID =  $this->calDavBackend->createCalendar($this->returnCalendarPrincipalUriForUser($calendarForUser), $calendarSlug, ['{DAV:}displayname' => $calendarTitle ]);
                $this->checkedIfCalendarExistsForUser[$calendarForUser] = $newCalendarID;
                return $newCalendarID;
            } catch (Exception $e) {
                return null;
            }
        }
        return $this->checkedIfCalendarExistsForUser[$calendarForUser];
    }

    private function returnCalendarPrincipalUriForUser($userID)
    {
        return self::CALENDAR_PRINCIPAL_URI_PREFIX.$userID;
    }

    public function createCalendarWithEventAndRemindersForUsersForCalTypeEvent($data)
    {
        $error = 0;

        $calTypeEventID = $data['event_id'];
        $calTypeEventTitle = $data['event_title'];
        $calTypeEventDescription = $data['event_description'];
        $calTypeEventDatetime = $data['event_datetime'];
        $calTypeEventEndDatetime = $data['event_end_datetime'];
        $calTypeEventExecuted = $data['event_executed'];
        $calTypeTitle = $data['event_cal_type_title'];
        $calTypeDescription = $data['event_cal_type_description'];
        $calTypeSlug = $data['event_cal_type_slug'];
        $calTypeEventAssignedUsers = $data['event_assigned_users'];
        $calTypeEventAssignedReminders = $data['event_assigned_reminders'];

        // start transaction
        $this->connection->beginTransaction();

        foreach ($calTypeEventAssignedUsers as $ind => $userID) {
            $calendarID = $this->createCalendarForUserIfCalendarNotExists($userID, $calTypeSlug, $calTypeTitle);
            if (!($calendarID > 0)) {
                $error++;
                break;
            }
            $resCreateEvent = $this->createCalendarEventWithReminders($calendarID, $calTypeEventTitle, $calTypeEventDescription, $calTypeEventDatetime, $calTypeEventEndDatetime, $calTypeEventAssignedReminders);
            if (!$resCreateEvent) {
                $error++;
                break;
            }
        }

        // mark calendar type event as executed
        if (!$error) {
            $calTypeEvent = $this->calendarTypeEventMapper->find($calTypeEventID);
            $dateTimeOfExecution = date('Y-m-d H:i:s', time());
            $calTypeEvent->setExecuted($dateTimeOfExecution);
            $res = $this->calendarTypeEventMapper->update($calTypeEvent);
        }

        // close transaction
        (!$error) ? $this->connection->commit() : $this->connection->rollBack();

        return (!$error);
    }


    public function getCalendarEventObject($id, $objectUri, $calendarType)
    {
        $query = $this->connection->getQueryBuilder();
        $query->select('*')
            ->from('calendarobjects')
            ->where($query->expr()->eq('calendarid', $query->createNamedParameter($id)))
            ->andWhere($query->expr()->eq('uri', $query->createNamedParameter($objectUri)))
            ->andWhere($query->expr()->eq('calendartype', $query->createNamedParameter($calendarType)));
        $stmt = $query->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if(!$row) {
            return null;
        }

        return $row;
    }

}