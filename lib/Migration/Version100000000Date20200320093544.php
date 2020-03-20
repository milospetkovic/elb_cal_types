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

        if (!$schema->hasTable('elb_cal_type_reminders')) {

            $table = $schema->createTable('elb_cal_type_reminders');

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

            $table->addColumn('fk_elb_cal_type', 'integer', [
                'notnull' => true,
                'length' => 11,
                'unsigned' => false
            ]);

            $table->addColumn('fk_elb_def_reminder', 'integer', [
                'notnull' => true,
                'length' => 11,
                'unsigned' => false
            ]);

            $table->setPrimaryKey(['id']);

            $table->addIndex(['user_author'], 'idx_ectr_uauthor');

            $table->addForeignKeyConstraint('oc_elb_calendar_types', ['fk_elb_cal_type'], ['id'], [ 'onDelete' => 'CASCADE' ], 'frgk_ect_id');

            $table->addForeignKeyConstraint('oc_elb_cal_def_reminders', ['fk_elb_def_reminder'], ['id'], [], 'frgk_ectdr_id');
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
