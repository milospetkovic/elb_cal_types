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
class Version100000000Date20200408145400 extends SimpleMigrationStep
{
    private $table_name = 'elb_cal_type_events';

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

            $table->addColumn('created_at', 'datetime', [
                'notnull' => true
            ])->setComment('Datetime when record was created');

            $table->addColumn('user_author', 'string', [
                'notnull' => true,
                'length' => 40,
            ])->setComment('User who created the record');;

            $table->addColumn('fk_gf_cal_type', 'integer', [
                'notnull' => true,
                'length' => 11,
                'unsigned' => false
            ])->setComment('Foreign key to the group folder calendar types table');

            $table->addColumn('title', 'string', [
                'notnull' => true,
                'length' => 100
            ])->setComment("Calendar's event title");

            $table->addColumn('description', 'text', [
                'notnull' => false,
                'default' => null
            ])->setComment("Calendar's event description");

            $table->addColumn('event_datetime', 'datetime', [
                'notnull' => true
            ])->setComment('Datetime when calendar event starts');

            $table->addColumn('executed', 'datetime', [
                'notnull' => false,
                'default' => null,
            ])->setComment('Shows datetime when assigned calendar event for users is executed');

            $table->setPrimaryKey(['id']);

            $table->addIndex(['user_author'], 'idx_ecte_uauthor');

            $table->addForeignKeyConstraint('oc_elb_gf_cal_types', ['fk_gf_cal_type'], ['id'], ['onUpdate' => 'RESTRICT', 'onDelete' => 'RESTRICT'], 'frgk_ecte_egct_id');
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
