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
class Version100000000Date20200401115125 extends SimpleMigrationStep
{
    private $table_name = 'elb_gf_cal_types';

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

            $table->addColumn('fk_elb_cal_type', 'integer', [
                'notnull' => true,
                'length' => 11,
                'unsigned' => false
            ])->setComment('Foreign key to the calendar types table');;

            $table->addColumn('fk_group_folder', 'bigint', [
                'notnull' => true,
                'length' => 11,
                'unsigned' => false
            ])->setComment('Foreign key to the group folders table');

            $table->setPrimaryKey(['id']);

            $table->addIndex(['user_author'], 'idx_egfct_uauthor');

            $table->addUniqueIndex(['fk_elb_cal_type', 'fk_group_folder'], 'unqk_egfct_caltype_gf');

            $table->addForeignKeyConstraint('oc_elb_calendar_types', ['fk_elb_cal_type'], ['id'], [ 'onUpdate' => 'RESTRICT', 'onDelete' => 'CASCADE' ], 'frgk_egfct_ect_id');

            $table->addForeignKeyConstraint('oc_group_folders', ['fk_group_folder'], ['folder_id'], [ 'onUpdate' => 'RESTRICT', 'onDelete' => 'CASCADE' ], 'frgk_egfct_gf_id');
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
