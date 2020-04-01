<?php


namespace OCA\ElbCalTypes\Controller;



use OCA\ElbCalTypes\Service\ElbCalTypeGroupFolderService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ElbcaltypegroupfolderController extends Controller
{

    /**
     * @var ElbCalTypeGroupFolderService
     */
    private $calTypeGroupFolderService;

    public function __construct($appName,
                                IRequest $request,
                                ElbCalTypeGroupFolderService $calTypeGroupFolderService)
    {
        parent::__construct($appName, $request);
        $this->calTypeGroupFolderService = $calTypeGroupFolderService;
    }

    /**
     * Get assigned group folders for calendar types
     */
    public function getassignedgroupfolders()
    {
        return new JSONResponse($this->calTypeGroupFolderService->returnAssignedGroupFoldersForCalendarTypes());
    }

    /**
     * Assign group folder(s) for calendar type
     *
     * @return JSONResponse
     */
    public function assigngroupfolderstocaltype()
    {
        return new JSONResponse($this->calTypeGroupFolderService->assignGroupFoldersForCalendarTypeID($this->request->params));
    }

}