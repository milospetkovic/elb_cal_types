<?php

namespace OCA\ElbCalTypes\Manager;

use OCA\DAV\CalDAV\CalDavBackend;
use OCP\IDBConnection;
use OCP\IL10N;
use Sabre\DAV\Exception;
use Sabre\DAV\UUIDUtil;

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
     * CalendarManager constructor.
     * @param CalDavBackend $calDavBackend
     * @param IL10N $l
     * @param IDBConnection $connection
     */
    public function __construct(CalDavBackend $calDavBackend,
                                IL10N $l,
                                IDBConnection $connection)
    {
        $this->calDavBackend = $calDavBackend;
        $this->l = $l;
        $this->connection = $connection;
    }







    /**
     * Create calendar event for shared file with expiration date
     *
     * @param $calendarID
     * @param $shareData
     * @return bool
     * @throws Exception\BadRequest
     */
    public function createCalendarEvent($calendarID, $shareData)
    {
        // uuid for .ics
        $calDataUri[0] = strtoupper(UUIDUtil::getUUID()) . '.ics';

        $userInitiatorForShare = $shareData['uid_initiator'];

        $shareTarget = trim($shareData['file_target'], '/');

        $currentTimeFormat = date('Ymd');

        // the datetime when calendar event object is created
        $createdDateTime = date('Ymd\THis\Z');

        // the start date time of calendar event
        $startDateTimeOfEvent = $createdDateTime;

        // uuid for calendar object itself
        $calObjectUUID[0] = strtolower(UUIDUtil::getUUID());

        // the name for calendar event
        $eventSummaryRaw = $this->l->t("User {user} shared {file} with you");
        $eventSummary = $this->translateSharedFileCalenderEvent($eventSummaryRaw, ['user' => $userInitiatorForShare, 'file' => $shareTarget]);

        // the end datetime of calendar event
        $endDateTimeOfEvent = $startDateTimeOfEvent;

        $endDateTimeFormat = date('Ymd', strtotime($shareData['expiration']));


        $timeZone = 'Europe/Belgrade';

        // populate start calendar event with data
        $calData[0] = <<<EOD
BEGIN:VCALENDAR
PRODID:-//IDN nextcloud.com//Calendar app 2.0.1//EN
CALSCALE:GREGORIAN
VERSION:2.0
BEGIN:VEVENT
CREATED:$createdDateTime
DTSTAMP:$createdDateTime
LAST-MODIFIED:$createdDateTime
SEQUENCE:2
UID:$calObjectUUID[0]
DTSTART;TZID=$timeZone:$startDateTimeOfEvent
DTEND;TZID=$timeZone:$endDateTimeOfEvent
LAST-MODIFIED;VALUE=DATE-TIME:$createdDateTime
DTSTAMP;VALUE=DATE-TIME:$createdDateTime
SUMMARY:$eventSummary
END:VEVENT
BEGIN:VTIMEZONE
TZID:$timeZone
BEGIN:DAYLIGHT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:CEST
DTSTART:19700329T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:CET
DTSTART:19701025T030000
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
END:STANDARD
END:VTIMEZONE
END:VCALENDAR
EOD;

        $calendarType = CalDavBackend::CALENDAR_TYPE_CALENDAR;

        // call method which executes creating calendar event(s)
        foreach ($calData as $ind => $calEventData) {

            $response = $this->calDavBackend->createCalendarObject($calendarID, $calDataUri[$ind], $calEventData, $calendarType);

            if (!strlen($response)) {
                return false; // error during saving cal. event
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
            //$this->createCalendarEvent($calendarID, $elem);
        }

        // close transaction
        (!$error) ? $this->connection->commit() : $this->connection->rollBack();

        return (!$error);

//        var_dump($data);
//        die('stopppp');
    }

}