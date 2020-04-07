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

    /**
     * Assign reminders for calendar type
     *
     * @return JSONResponse
     */
    public function assigndefreminderstocaltype()
    {
        return new JSONResponse($this->calTypeReminderService->assignRemindersForCalendarTypeID($this->request->params));
    }

    /**
     * Remove reminder assigned for calendar type
     *
     * @return JSONResponse
     */
    public function removereminderforcaltype()
    {
        return new JSONResponse($this->calTypeReminderService->removeReminderForCalendarTypeID($this->request->params));
    }

    /**
     * Get assigned reminders for calendar types
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function getassignedremindersforcaltypesids()
    {
        return new JSONResponse($this->calTypeReminderService->getAssignedRemindersForCalTypesIds($this->request->params['calTypesIds']));
    }

}