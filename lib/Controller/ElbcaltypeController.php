<?php

namespace OCA\ElbCalTypes\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\ElbCalTypes\Service\ElbCalTypesService;

class ElbcaltypeController extends Controller {

    /** @var ElbCalTypesService */
    private $service;

    /** @var string */
    private $userId;

    public function __construct($appName,
                                IRequest $request,
                                ElbCalTypesService $service,
                                $userId) {
        parent::__construct($appName, $request);
        $this->service = $service;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
     */
    public function index(): DataResponse {
        return new DataResponse($this->service->findAll($this->userId));
    }


}
