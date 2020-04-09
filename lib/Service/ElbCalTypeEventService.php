<?php


namespace OCA\ElbCalTypes\Service;


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

    public function __construct(CalendarTypeEventMapper $calendarTypeEventMapper,
                                CalendarTypeEvent $calendarTypeEvent)
    {
        $this->calendarTypeEventMapper = $calendarTypeEventMapper;
        $this->calendarTypeEvent = $calendarTypeEvent;
    }


    public function storeCalendarTypeEvent($data)
    {
        $calTypeId = $data['caltypeid'];
        $eventName = $data['eventname'];
        $eventDescription = $data['eventdesc'];
        $eventDateTime = $data['eventdatetime'];
        $eventAssignedReminders = $data['reminders'];
        $eventAssignedUsers = $data['assigneduser'];

        var_dump($data);
        die('stop');
    }

}