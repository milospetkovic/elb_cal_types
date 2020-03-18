<?php
namespace OCA\ElbCalTypes\Service;

use Exception;

use OCA\ElbCalTypes\Controller\Errors;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\ElbCalTypes\Db\CalendarTypes;
use OCA\ElbCalTypes\Db\CalendarTypesMapper;


class ElbCalTypesService
{

    use Errors;

    /** @var CalendarTypesMapper */
    private $mapper;

    public function __construct(CalendarTypesMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findAll(): array
    {
        return $this->mapper->findAll();
    }

    private function handleException (Exception $e): void
    {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new ElbCalTypeNotFound($e->getMessage());
        } else {
            throw $e;
        }
    }

    public function find($id)
    {
        try {
            return $this->mapper->find($id);
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function create($title, $description, $userId)
    {
        $calType = new CalendarTypes();
        $calType->setCreatedAt(date('Y-m-d H:i:s', time()));
        $calType->setUserAuthor($userId);
        $calType->setTitle($title);
        $calType->setSlug($calType->slugify('title'));
        $calType->setDescription($description);
        return $this->mapper->insert($calType);
    }

    public function update($id, $title, $description, $userId)
    {
        try {
            $calType = $this->mapper->find($id);
            $calType->setTitle($title);
            $calType->setDescription($description);
            return $this->mapper->update($calType);
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function delete($id)
    {
        try {
            $calType = $this->mapper->find($id);
            $this->mapper->delete($calType);
            return $calType;
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

}