<?php


namespace OCA\ElbCalTypes\Controller;



use OCP\AppFramework\Controller;
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

}