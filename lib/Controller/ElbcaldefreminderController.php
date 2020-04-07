<?php


namespace OCA\ElbCalTypes\Controller;


use OCA\ElbCalTypes\Service\ElbCalDefRemindersService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ElbcaldefreminderController extends Controller
{
    /**
     * @var ElbCalDefRemindersService
     */
    private $calDefReminderService;

    /**
     * ElbcaldefreminderController constructor.
     * @param ElbCalDefRemindersService $calDefReminderService
     */
    public function __construct($appName,
                                IRequest $request,
                                ElbCalDefRemindersService $calDefReminderService)
    {
        parent::__construct($appName, $request);
        $this->calDefReminderService = $calDefReminderService;
    }

    /**
     * Get all default calendar reminders
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getdefaultreminders()
    {
        return new JSONResponse($this->calDefReminderService->findAll());
    }

}