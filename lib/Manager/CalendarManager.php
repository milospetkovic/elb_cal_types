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

    CONST CALENDAR_URI = 'test-cal-123';

    /**
     * @var CalDavBackend
     */
    private $calDavBackend;

    private $checkedIfCalendarExistsForUser = [];

    private $calendarDisplayName = 'Test';

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
     * Translation for calendar's name
     *
     * @return string
     */
    private function getCalendarDisplayName()
    {
        return $this->l->t($this->calendarDisplayName);
    }

    /**
     * Create a calendar which will hold all events for shared files with expiration date for a user (if the calendar doesn't exist)
     * and create event(s) in the calendar
     */
    public function creteCalendarAndEventForUser()
    {


        // start transaction
        $this->connection->beginTransaction();

        try {
            foreach ($rows as $ind => $elem) {
                $calendarID = $this->createCalendarForUserIfCalendarNotExists($elem['share_with']);
                $this->createCalendarEvent($calendarID, $elem);
            }
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            echo 'Exception: '.$e->getMessage();
            echo ' DB error: '.$this->connection->errorInfo();
        }

    }

    /**
     * Replace placeholders with data from parameters variable
     *
     * @param $subject
     * @param array $parameters
     * @return string|string[]
     */
    private function translateSharedFileCalenderEvent($subject, array $parameters)
    {
        foreach ($parameters as $paramKey => $paramVal) {
            $subject = str_replace('{'.$paramKey.'}', $paramVal, $subject);
        }
        return $subject;
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

    /**
     * Create a calendar for shared files which have expiration date for user (if the calendar doesn't exist, otherwise return ID of the existing calendar.
     *
     * @param $calendarForUser
     * @return int|mixed|null
     */
    public function createCalendarForUserIfCalendarNotExists($calendarForUser)
    {
        if (!array_key_exists($calendarForUser, $this->checkedIfCalendarExistsForUser)) {

            // fetch existing calendars for user
            $existingCalendarsForUser = $this->calDavBackend->getCalendarsForUser(self::CALENDAR_PRINCIPAL_URI_PREFIX.$calendarForUser);

            // check up if calendar already exists (return id of calendar in that case)
            if (is_array($existingCalendarsForUser) && count($existingCalendarsForUser)) {
                foreach ($existingCalendarsForUser as $ind => $arr) {
                    if ($arr['uri'] == self::CALENDAR_URI) {
                        $this->checkedIfCalendarExistsForUser[$calendarForUser] = $arr['id'];
                        return $arr['id'];
                    }
                }
            }

            // calendar doesn't exist -> create a calendar for the user
            try {
                $newCalendarID =  $this->calDavBackend->createCalendar(self::CALENDAR_PRINCIPAL_URI_PREFIX . $calendarForUser, self::CALENDAR_URI, ['{DAV:}displayname' => $this->getCalendarDisplayName()]);
                $this->checkedIfCalendarExistsForUser[$calendarForUser] = $newCalendarID;
                return $newCalendarID;
            } catch (Exception $e) {
                return null;
            }
        }
        return $this->checkedIfCalendarExistsForUser[$calendarForUser];
    }

}