<?php


namespace OCA\ElbCalTypes\Controller;

use OCA\ElbCalTypes\Service\ElbGroupFoldersService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ElbgroupfolderController extends Controller
{

    /**
     * @var ElbGroupFoldersService
     */
    private $groupFoldersService;

    public function __construct($appName,
                                IRequest $request,
                                ElbGroupFoldersService $groupFoldersService)
    {
        $this->groupFoldersService = $groupFoldersService;
    }

    /**
     * Get all group folders
     */
    public function getallgroupfolders()
    {
        return new JSONResponse($this->groupFoldersService->findAll());
    }

}