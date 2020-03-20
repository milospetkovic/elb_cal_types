<?php


namespace OCA\ElbCalTypes\Controller;


use OCA\ElbCalTypes\Service\ElbCalDefRemindersService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;

class ElbcalreminderController extends Controller
{
    /**
     * @var ElbCalDefRemindersService
     */
    private $calDefReminderService;

    /**
     * ElbcalreminderController constructor.
     * @param ElbCalDefRemindersService $calDefReminderService
     */
    public function __construct(ElbCalDefRemindersService $calDefReminderService)
    {
        $this->calDefReminderService = $calDefReminderService;
    }

    /**
     * Get all default calendar reminders
     *
     */
    public function getdefaultreminders()
    {
        return new JSONResponse($this->calDefReminderService->findAll());
    }

}