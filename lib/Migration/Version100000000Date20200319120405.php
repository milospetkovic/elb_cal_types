<?php

declare(strict_types=1);

namespace OCA\ElbCalTypes\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version100000000Date20200319120405 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options)
    {

	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('elb_cal_def_reminders')) {

            $table = $schema->createTable('elb_cal_def_reminders');

            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);

            $table->addColumn('created_at', 'datetime', [
                'notnull' => true
            ]);

            $table->addColumn('user_author', 'string', [
                'notnull' => true,
                'length' => 40,
            ]);

            $table->addColumn('title', 'string', [
                'notnull' => true,
                'length' => 100
            ]);

            $table->addColumn('minutes_before_event', 'integer', [
                'notnull' => true,
                'unsigned' => false,
                'length' => 11
            ]);

            $table->setPrimaryKey(['id']);

            $table->addIndex(['user_author'], 'idx_ectr_uauthor');
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
