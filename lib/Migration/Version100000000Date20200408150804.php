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
class Version100000000Date20200408150804 extends SimpleMigrationStep {

    private $table_name = 'elb_event_reminders';

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
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable($this->table_name)) {

            $table = $schema->createTable($this->table_name);

            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ])->setComment('Primary key');

            $table->addColumn('fk_cal_type_event', 'integer', [
                'notnull' => true,
                'length' => 11,
                'unsigned' => false
            ])->setComment('Foreign key to the calendar type event table');

            $table->addColumn('fk_cal_def_reminder', 'integer', [
                'notnull' => true,
                'length' => 11,
                'unsigned' => false
            ])->setComment('Foreign key to the calendar default reminders table');

            $table->setPrimaryKey(['id']);

            $table->addForeignKeyConstraint('oc_elb_cal_type_events', ['fk_cal_type_event'], ['id'], ['onUpdate' => 'RESTRICT', 'onDelete' => 'CASCADE'], 'frgk_ecter_ecte_id');

            $table->addForeignKeyConstraint('oc_elb_cal_def_reminders', ['fk_cal_def_reminder'], ['id'], ['onUpdate' => 'RESTRICT', 'onDelete' => 'RESTRICT'], 'frgk_ecter_ecdr_id');
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
