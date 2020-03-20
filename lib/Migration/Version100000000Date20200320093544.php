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
class Version100000000Date20200320093544 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options)
    {
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

            $table->addColumn('modified_at', 'datetime', [
                'notnull' => false,
                'default' => null
            ]);

            $table->addColumn('user_author', 'string', [
                'notnull' => true,
                'length' => 40,
            ]);

            $table->addColumn('user_modifier', 'string', [
                'notnull' => false,
                'default' => null,
                'length' => 40,
            ]);

            $table->addColumn('title', 'string', [
                'notnull' => true,
                'length' => 100
            ]);

            $table->addColumn('minutes_before_event', 'integer', [
                'notnull' => true,
                'unsigned' => false,
                'length' => 10
            ]);

            $table->setPrimaryKey(['id']);

            $table->addIndex(['user_author'], 'idx_ectdr_uauthor');

            $table->addIndex(['user_modifier'], 'idx_ectdr_umodifier');

        }
        return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		return null;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}
}
