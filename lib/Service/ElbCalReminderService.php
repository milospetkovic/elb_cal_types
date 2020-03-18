<?php


namespace OCA\ElbCalTypes\Service;


use OCP\IL10N;

class ElbCalReminderService
{
    /**
     * @var IL10N
     */
    private $l;

    public function __construct(IL10N $l)
    {
        $this->l = $l;
    }

    public function getAvailableReminders()
    {
        return [
            0 => $this->l->t('at the event\'s start'),
            1 => $this->l->t('1 minute before the event starts'),
            2 => $this->l->t('2 minutes before the event starts'),
            5 => $this->l->t('5 minutes before the event starts'),
            10 => $this->l->t('10 minutes before the event starts'),
            15 => $this->l->t('15 minutes before the event starts'),
            20 => $this->l->t('20 minutes before the event starts'),
            30 => $this->l->t('30 minutes before the event starts'),
            45 => $this->l->t('45 minutes before the event starts'),
            60 => $this->l->t('1 hour before the event starts'),
            120 => $this->l->t('2 hours before the event starts'),
            180 => $this->l->t('3 hours before the event starts'),
            360 => $this->l->t('6 hours before the event starts'),
            1440 => $this->l->t('1 day before the event starts'),
            2880 => $this->l->t('2 days before the event starts'),
            4320 => $this->l->t('3 days before the event starts'),
            7200 => $this->l->t('5 days before the event starts'),
            10080 => $this->l->t('1 week before the event starts'),
            20160 => $this->l->t('2 weeks before the event starts'),
            43200 => $this->l->t('1 month before the event starts'),
            86400 => $this->l->t('2 months before the event starts'),
            129600 => $this->l->t('3 months before the event starts'),
            259200 => $this->l->t('6 months before the event starts'),
            525600 => $this->l->t('1 year before the event starts'),
        ];
    }

}