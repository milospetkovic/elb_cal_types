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
class Version100000000Date20200408152825 extends SimpleMigrationStep
{
    private $table_name = 'elb_event_users';

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

            $table->addColumn('fk_user', 'string', [
                'notnull' => true,
                'length' => 64,
                'unsigned' => true
            ])->setComment('Foreign key to the user to whom the calendar event is assigned');

            $table->setPrimaryKey(['id']);

            $table->addForeignKeyConstraint('oc_elb_cal_type_events', ['fk_cal_type_event'], ['id'], ['onUpdate' => 'RESTRICT', 'onDelete' => 'CASCADE'], 'frgk_ecteu_ecte_id');

            $table->addForeignKeyConstraint('oc_users', ['fk_user'], ['uid'], ['onUpdate' => 'RESTRICT', 'onDelete' => 'RESTRICT'], 'frgk_ecteu_u_uid');
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
