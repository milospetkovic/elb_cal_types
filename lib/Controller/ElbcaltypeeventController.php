<?php


namespace OCA\ElbCalTypes\Controller;


use OCA\ElbCalTypes\Service\ElbCalTypeEventService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IConfig;
use OCP\IRequest;

class ElbcaltypeeventController extends Controller
{

    /**
     * @var ElbCalTypeEventService
     */
    private $calTypeEventService;
    /**
     * @var IConfig
     */
    private $config;

    /**
     * ElbcaltypeeventController constructor.
     * @param $appName
     * @param IRequest $request
     * @param ElbCalTypeEventService $calTypeEventService
     * @param IConfig $config
     */
    public function __construct($appName,
                                IRequest $request,
                                ElbCalTypeEventService $calTypeEventService,
                                IConfig $config)
    {
        parent::__construct($appName, $request);
        $this->calTypeEventService = $calTypeEventService;
        $this->config = $config;
        $this->setDefaultDateTimeZone();
    }

    /**
     * Set default time zone (needed for time() function)
     */
    public function setDefaultDateTimeZone()
    {
        date_default_timezone_set($this->config->getSystemValue('logtimezone', "Europe/Berlin"));
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
        //return $this->calTypeEventService->getCalendarTypeEvents($this->request->params);
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
        return new JSONResponse($this->calTypeEventService->saveCalendarEventForUsersForCalendarTypeEventID($this->request->params));
    }

}