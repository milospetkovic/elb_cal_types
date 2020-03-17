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
        return new DataResponse($this->service->findAll($this->userId));
    }

    /**
     * Create a calendar type
     */
    public function create(string $title, string $description): DataResponse
    {
        return new DataResponse($this->service->create($title, $description,
            $this->userId));
    }

    /**
     * Update a calendar type
     */
    public function update(int $id, string $title, string $description): DataResponse
    {
        return $this->handleNotFound(function () use ($id, $title, $description) {
            return $this->service->update($id, $title, $description, $this->userId);
        });
    }

    /**
     * Delete a calendar type
     */
    public function destroy(int $id): DataResponse
    {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->delete($id, $this->userId);
        });
    }


}
