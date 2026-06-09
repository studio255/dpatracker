<?php
declare(strict_types=1);

namespace OCA\DpaTracker\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1000Date20260607120000 extends SimpleMigrationStep {
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('dpatracker_subproc')) {
            $table = $schema->createTable('dpatracker_subproc');

            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('user_id', 'string', ['notnull' => true, 'length' => 64]);
            $table->addColumn('name', 'string', ['notnull' => true, 'length' => 255]);
            $table->addColumn('purpose', 'text', ['notnull' => false]);
            $table->addColumn('data_categories', 'text', ['notnull' => false]);
            $table->addColumn('location', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('us_parent', 'boolean', ['notnull' => true, 'default' => false]);
            $table->addColumn('dpa_file_id', 'integer', ['notnull' => false]);
            $table->addColumn('review_date', 'date', ['notnull' => false]);
            $table->addColumn('created_at', 'datetime', ['notnull' => false]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'dpatracker_uid_idx');
        }

        return $schema;
    }
}