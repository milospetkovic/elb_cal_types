<?php
namespace OCA\ElbCalTypes\Service;

use Exception;


use OCA\Activity\CurrentUser;
use OCA\ElbCalTypes\Controller\Errors;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\ElbCalTypes\Db\CalendarTypes;
use OCA\ElbCalTypes\Db\CalendarTypesMapper;


class ElbCalTypesService
{
    use Errors;

    /**
     * Calendar type slug (id of created calendar type will be added to the end of the string)
     * i.e. elb-caltype-50 (elb calendar type id=50)
     *
     * @var string
     */
    private $elbCalendarTypeSlugPrefix = 'elb-caltype-';

    /** @var CalendarTypesMapper */
    private $mapper;

    /**
     * @var CurrentUser
     */
    private $currentUser;

    /**
     * ElbCalTypesService constructor.
     * @param CalendarTypesMapper $mapper
     * @param CurrentUser $currentUser
     */
    public function __construct(CalendarTypesMapper $mapper,
                                CurrentUser $currentUser)
    {
        $this->mapper = $mapper;
        $this->currentUser = $currentUser;
    }

    /**
     * Fetch all calendar types
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->mapper->findAll();
    }

    /**
     * Exception handler for calendar type
     *
     * @param Exception $e
     * @throws ElbCalTypeNotFound
     */
    private function handleException (Exception $e): void
    {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new ElbCalTypeNotFound($e->getMessage());
        } else {
            throw $e;
        }
    }

    /**
     * Get calendar type by it's id
     *
     * @param $id
     * @return CalendarTypes|Entity
     * @throws ElbCalTypeNotFound
     */
    public function find($id)
    {
        try {
            return $this->mapper->find($id);
        } catch(Exception $e) {
            $this->handleException($e);
        }
        return false;
    }

    /**
     * Create a calendar type
     *
     * @param $title
     * @param $description
     * @return CalendarTypes|Entity
     */
    public function create($title, $description)
    {
        $calType = new CalendarTypes();
        $calType->setCreatedAt(date('Y-m-d H:i:s', time()));
        $calType->setUserAuthor($this->currentUser->getUID());
        $calType->setTitle($title);
        $calType->setSlug($this->elbCalendarTypeSlugPrefix);
        $calType->setDescription($description);
        $res = $this->mapper->insert($calType);
        if ($res->id > 0) {
            $calType->setSlug($this->elbCalendarTypeSlugPrefix.$res->id);
            return $this->mapper->update($calType);
        }
        return $calType;
    }

    /**
     * Update a calendar type
     *
     * @param $id
     * @param $title
     * @param $description
     * @return Entity
     * @throws ElbCalTypeNotFound
     */
    public function update($id, $title, $description)
    {
        try {
            $calType = $this->mapper->find($id);
            $calType->setModifiedAt(date('Y-m-d H:i:s', time()));
            $calType->setUserModifier($this->currentUser->getUID());
            $calType->setTitle($title);
            $calType->setDescription($description);
            return $this->mapper->update($calType);
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Delete a calendar type with provided id
     *
     * @param $id
     * @return CalendarTypes|Entity
     * @throws ElbCalTypeNotFound
     */
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