<?php


namespace OCA\ElbCalTypes\Service;


use OCA\Activity\CurrentUser;
use OCA\ElbCalTypes\Db\CalendarTypeEvent;
use OCA\ElbCalTypes\Db\CalendarTypeEventMapper;

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

    public function __construct(CalendarTypeEventMapper $calendarTypeEventMapper,
                                CalendarTypeEvent $calendarTypeEvent,
                                CurrentUser $currentUser)
    {
        $this->calendarTypeEventMapper = $calendarTypeEventMapper;
        $this->calendarTypeEvent = $calendarTypeEvent;
        $this->currentUser = $currentUser;
    }

    public function storeCalendarTypeEvent($data)
    {
        $calTypeId = $data['caltypeid'];
        $eventName = $data['eventname'];
        $eventDescription = $data['eventdesc'];
        $eventDateTime = $data['eventdatetime'];

        // @TODO implement linking with those 2 related tables!!!
        $eventAssignedReminders = $data['reminders'];
        $eventAssignedUsers = $data['assigneduser'];

        $calTypeEvent = new $this->calendarTypeEvent;
        $calTypeEvent->setFkElbCalType($calTypeId);
        $calTypeEvent->setUserAuthor($this->currentUser->getUID());
        $calTypeEvent->setCreatedAt(date('Y-m-d H:i:s', time()));
        $calTypeEvent->setTitle($eventName);
        $calTypeEvent->setDescription($eventDescription);
        $calTypeEvent->setEventDatetime($eventDateTime);
        return $this->calendarTypeEventMapper->insert($calTypeEvent);


//        $calTypeReminder = new CalendarTypeReminder();
//        $calTypeReminder->setFkElbCalType($calendarTypeID);
//        $calTypeReminder->setFkElbDefReminder($defReminderID);
//        $calTypeReminder->setUserAuthor($this->currentUser->getUID());
//        $calTypeReminder->setCreatedAt(date('Y-m-d H:i:s', time()));
//        $this->mapper->insert($calTypeReminder);

        var_dump($data);
        die('stop');
    }

}