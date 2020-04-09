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

}