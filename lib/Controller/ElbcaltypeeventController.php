<?php


namespace OCA\ElbCalTypes\Controller;


use OCA\ElbCalTypes\Service\ElbCalTypeEventService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ElbcaltypeeventController extends Controller
{

    /**
     * @var ElbCalTypeEventService
     */
    private $calTypeEventService;

    public function __construct($appName,
                                IRequest $request,
                                ElbCalTypeEventService $calTypeEventService)
    {
        parent::__construct($appName, $request);

        $this->calTypeEventService = $calTypeEventService;
    }

    /**
     * Save calendar type event
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function saveacalendartypeevent()
    {
        return new JSONResponse($this->calTypeEventService->storeCalendarTypeEvent($this->request->params));
    }

    /**
     * Get calendar type events
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function getcalendartypeevents()
    {
        return new JSONResponse($this->calTypeEventService->getCalendarTypeEvents($this->request->params));
    }

    /**
     * Delete calendar type event
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function deletecaltypeevent()
    {
        return new JSONResponse($this->calTypeEventService->deleteCalendarTypeEvent($this->request->params));
    }

    /**
     * Save calendar event to assigned users for calendar type event
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @return JSONResponse
     */
    public function saveacalendareventforusers()
    {
        return new JSONResponse($this->calTypeEventService->saveCalendarEventForUsersforCalendarTypeEvent($this->request->params));
    }

}