<?php

namespace OCA\ElbCalTypes\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\ElbCalTypes\Service\ElbCalTypesService;

class ElbcaltypeController extends Controller
{
    /** @var ElbCalTypesService */
    private $service;

    /** @var string */
    private $userId;

    /**
     * trait for handling errors
     */
    use Errors;

    /**
     * ElbcaltypeController constructor.
     * @param $appName
     * @param IRequest $request
     * @param ElbCalTypesService $service
     * @param $userId
     */
    public function __construct($appName,
                                IRequest $request,
                                ElbCalTypesService $service,
                                $userId)
    {
        parent::__construct($appName, $request);
        $this->service = $service;
        $this->userId = $userId;
    }

    /**
     * Show all saved calendar types
     */
    public function index(): DataResponse
    {
        return new DataResponse($this->service->findAll());
    }

    /**
     * Create a calendar type
     * @param string $title
     * @param string $description
     * @return DataResponse
     */
    public function create(string $title, string $description): DataResponse
    {
        return new DataResponse($this->service->create($title, $description));
    }

    /**
     * Update a calendar type
     * @param int $id
     * @param string $title
     * @param string $description
     * @return DataResponse
     */
    public function update(int $id, string $title, string $description): DataResponse
    {
        return $this->handleNotFound(function () use ($id, $title, $description) {
            return $this->service->update($id, $title, $description);
        });
    }

    /**
     * Delete a calendar type
     * @param int $id
     * @return DataResponse
     */
    public function destroy(int $id): DataResponse
    {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->delete($id);
        });
    }

}
