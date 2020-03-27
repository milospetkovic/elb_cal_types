<?php


namespace OCA\ElbCalTypes\Controller;


use OCA\ElbCalTypes\Service\ElbCalTypeReminderService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ElbcaltypereminderController extends Controller
{

    /**
     * @var ElbCalTypeReminderService
     */
    private $calTypeReminderService;

    public function __construct($appName,
                                IRequest $request,
                                ElbCalTypeReminderService $calTypeReminderService)
    {
        parent::__construct($appName, $request);
        $this->calTypeReminderService = $calTypeReminderService;
    }

    /**
     * Get assigned reminders for calendar types
     */
    public function getassignedreminders()
    {
        return new JSONResponse($this->calTypeReminderService->returnAssignedRemindersForCalendarTypes());
    }

    public function assigndefreminderstocaltype()
    {
        return new JSONResponse($this->calTypeReminderService->assignedRemindersForCalendarTypeID($this->request->params));
    }



}