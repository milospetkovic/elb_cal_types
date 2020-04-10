<?php


namespace OCA\ElbCalTypes\Service;


use OCA\Activity\CurrentUser;
use OCA\ElbCalTypes\Db\CalendarTypeEvent;
use OCA\ElbCalTypes\Db\CalendarTypeEventMapper;
use OCA\ElbCalTypes\Db\CalendarTypeEventReminder;
use OCA\ElbCalTypes\Db\CalendarTypeEventReminderMapper;
use OCA\ElbCalTypes\Db\CalendarTypeEventUser;
use OCA\ElbCalTypes\Db\CalendarTypeEventUserMapper;

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

    public function __construct(CalendarTypeEventMapper $calendarTypeEventMapper,
                                CalendarTypeEvent $calendarTypeEvent,
                                CalendarTypeEventReminderMapper $calendarTypeEventReminderMapper,
                                CalendarTypeEventReminder $calendarTypeEventReminder,
                                CalendarTypeEventUserMapper $calendarTypeEventUserMapper,
                                CalendarTypeEventUser $calendarTypeEventUser,
                                CurrentUser $currentUser)
    {
        $this->calendarTypeEventMapper = $calendarTypeEventMapper;
        $this->calendarTypeEvent = $calendarTypeEvent;
        $this->calendarTypeEventReminderMapper = $calendarTypeEventReminderMapper;
        $this->calendarTypeEventReminder = $calendarTypeEventReminder;
        $this->currentUser = $currentUser;
        $this->calendarTypeEventUserMapper = $calendarTypeEventUserMapper;
        $this->calendarTypeEventUser = $calendarTypeEventUser;
    }

    // @TODO implement validation and empty values as null...
    public function storeCalendarTypeEvent($data)
    {
        $calTypeId = $data['caltypeid'];
        (empty($data['eventname']) ? $eventName=null : $eventName=trim($data['eventname']));
        $eventDescription = $data['eventdesc'];
        $eventDateTime = $data['eventdatetime'];
        $eventAssignedReminders = $data['reminders'];
        $eventAssignedUsers = $data['assignedusers'];

        $calTypeEvent = new $this->calendarTypeEvent;
        $calTypeEvent->setFkElbCalType($calTypeId);
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

}