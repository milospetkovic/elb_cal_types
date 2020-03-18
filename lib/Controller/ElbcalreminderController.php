<?php


namespace OCA\ElbCalTypes\Controller;


use OCA\ElbCalTypes\Service\ElbCalReminderService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;

class ElbcalreminderController extends Controller
{

    /**
     * @var ElbCalReminderService
     */
    private $calReminderService;

    public function __construct(ElbCalReminderService $calReminderService)
    {
        $this->calReminderService = $calReminderService;
    }

    /**
     * Get all default calendar reminders
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getallreminders()
    {
        return new JSONResponse($this->calReminderService->getAvailableReminders());
    }

}