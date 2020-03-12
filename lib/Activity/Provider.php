<?php
/**
 * @copyright Copyright (c) 2020 Milos Petkovic <milos.petkovic@elb-solutions.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\ElbCalTypes\Activity;

use InvalidArgumentException;
use OCP\Activity\IEvent;
use OCP\Activity\IEventMerger;
use OCP\Activity\IManager;
use OCP\Activity\IProvider;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\L10N\IFactory;

class Provider implements IProvider
{

    /** @var IFactory */
    protected $languageFactory;

    /** @var IL10N */
    protected $l;

    /** @var IURLGenerator */
    protected $url;

    /** @var IManager */
    protected $activityManager;

    /** @var IUserManager */
    protected $userManager;

    /** @var IEventMerger */
    protected $eventMerger;

    /** @var array */
    protected $displayNames = [];

    /** @var string */
    protected $lastType = '';

    /**
     * @param IFactory $languageFactory
     * @param IURLGenerator $url
     * @param IManager $activityManager
     * @param IUserManager $userManager
     * @param IEventMerger $eventMerger
     */
    public function __construct(IFactory $languageFactory,
                                IURLGenerator $url,
                                IManager $activityManager,
                                IUserManager $userManager,
                                IEventMerger $eventMerger)
    {
        $this->languageFactory = $languageFactory;
        $this->url = $url;
        $this->activityManager = $activityManager;
        $this->userManager = $userManager;
        $this->eventMerger = $eventMerger;
    }

    /**
     * @param string $language
     * @param IEvent $event
     * @param IEvent|null $previousEvent
     * @return IEvent
     * @throws InvalidArgumentException
     * @since 11.0.0
     */
    public function parse($language, IEvent $event, IEvent $previousEvent = null)
    {
        if ($event->getApp() !== 'elb_cal_types') {
            throw new InvalidArgumentException();
        }

        $this->l = $this->languageFactory->get('elb_cal_types', $language);
        if (method_exists($this->activityManager, 'getRequirePNG') && $this->activityManager->getRequirePNG()) {
            $event->setIcon($this->url->getAbsoluteURL($this->url->imagePath('core', 'actions/share.png')));
        } else {
            $event->setIcon($this->url->getAbsoluteURL($this->url->imagePath('core', 'actions/share.svg')));
        }

        if ($this->activityManager->isFormattingFilteredObject()) {
            try {
                return $this->parseShortVersion($event, $previousEvent);
            } catch (InvalidArgumentException $e) {
                // Ignore and simply use the long version...
            }
        }

        return $this->parseLongVersion($event, $previousEvent);
    }

    /**
     * @param IEvent $event
     * @param IEvent $previousEvent
     * @return IEvent
     * @throws InvalidArgumentException
     * @since 11.0.0
     */
    public function parseShortVersion(IEvent $event, IEvent $previousEvent = null)
    {
        $parsedParameters = $this->getParsedParameters($event);
        $params = $event->getSubjectParameters();

        $subject = '';

        $this->setSubjects($event, $subject, $parsedParameters);

        if ($this->lastType !== $params[2]) {
            $this->lastType = $params[2];
            return $event;
        }

        return $this->eventMerger->mergeEvents('actor', $event, $previousEvent);
    }

    /**
     * @param IEvent $event
     * @param IEvent $previousEvent
     * @return IEvent
     * @throws InvalidArgumentException
     * @since 11.0.0
     */
    public function parseLongVersion(IEvent $event, IEvent $previousEvent = null)
    {
        $parsedParameters = $this->getParsedParameters($event);

        $params = $event->getSubjectParameters();

        $subject = $this->getTranslatedStringBySubjectOfEvent($event->getSubject());

        $this->setSubjects($event, $subject, $parsedParameters);

        if ($this->lastType !== $params[2]) {
            $this->lastType = $params[2];
            return $event;
        }

        $event = $this->eventMerger->mergeEvents('actor', $event, $previousEvent);
        if ($event->getChildEvent() === null) {
            $event = $this->eventMerger->mergeEvents('file', $event, $previousEvent);
        }

        return $event;
    }

    private function getTranslatedStringBySubjectOfEvent($activitySubject)
    {
        switch ($activitySubject) {
            default:
                $subject = '';
        }
        return $subject;
    }

    /**
     * @param IEvent $event
     * @param string $subject
     * @param array $parameters
     * @throws InvalidArgumentException
     */
    protected function setSubjects(IEvent $event, $subject, array $parameters)
    {
        $placeholders = $replacements = [];
        foreach ($parameters as $placeholder => $parameter) {
            $placeholders[] = '{' . $placeholder . '}';
            if ($parameter['type'] === 'file') {
                $replacements[] = $parameter['path'];
            } else {
                $replacements[] = $parameter['name'];
            }
        }

        $event->setParsedSubject(str_replace($placeholders, $replacements, $subject))
            ->setRichSubject($subject, $parameters);
    }

    protected function getParsedParameters(IEvent $event)
    {
        return [];
    }

}
