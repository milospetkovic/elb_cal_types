<?php


namespace OCA\ElbCalTypes\Controller;


use OCA\ElbCalTypes\Service\ElbCalReminderService;
use OCP\AppFramework\Http\JSONResponse;

class ElbcalreminderController
{

    /**
     * @var ElbCalReminderService
     */
    private $calReminderService;

    public function __construct(ElbCalReminderService $calReminderService)
    {
        $this->calReminderService = $calReminderService;
    }

    public function getAllReminders()
    {
        return JSONResponse($this->calReminderService->getAvailableReminders());
    }

}