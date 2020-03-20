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

    public function __construct(IL10N $l,
                                CalendarTypeReminderMapper $mapper)
    {
        $this->l = $l;
        $this->mapper = $mapper;
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
        $results = $this->mapper->getAssignedRemindersForCalendarTypes();
        var_dump($results);
        die('stop');
    }

}