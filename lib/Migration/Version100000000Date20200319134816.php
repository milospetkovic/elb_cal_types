<?php

declare(strict_types=1);

namespace OCA\ElbCalTypes\Migration;

use Closure;
use OCA\Activity\CurrentUser;
use OCA\ElbCalTypes\Service\ElbCalReminderService;
use OCP\DB\ISchemaWrapper;
use OCP\IDBConnection;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version100000000Date20200319134816 extends SimpleMigrationStep
{
    /**
     * @var IDBConnection
     */
    private $connection;
    /**
     * @var ElbCalReminderService
     */
    private $calReminderService;
    /**
     * @var CurrentUser
     */
    private $currentUser;

    public function __construct(IDBConnection $connection,
                                ElbCalReminderService $calReminderService,
                                CurrentUser $currentUser)
    {
        $this->connection = $connection;
        $this->calReminderService = $calReminderService;
        $this->currentUser = $currentUser;
    }

    /**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options)
    {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if ($schema->hasTable('elb_cal_def_reminders')) {

            if (count($this->calReminderService->getAvailableReminders())) {
                $query = $this->connection->getQueryBuilder();
                $query->insert('elb_cal_def_reminders')
                    ->values([
                        'created_at' => $query->createParameter('created_at'),
                        'user_author' => $query->createParameter('user_author'),
                        'title' => $query->createParameter('title'),
                        'minutes_before_event' => $query->createParameter('minutes_before_event'),
                    ]);

                foreach ($this->calReminderService->getAvailableReminders() as $min => $title) {
                    $query->setParameter('created_at', date('Y-m-d H:i:s', time()));
                    $query->setParameter('user_author', 'admin2');
                    $query->setParameter('title', $title);
                    $query->setParameter('minutes_before_event', $min);

                    $query->execute();
                }
            }
        }
        return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}
}
