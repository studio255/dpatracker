<?php

declare(strict_types=1);

namespace OCA\DpaTracker\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1000Date20260608120000 extends SimpleMigrationStep {
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();
		$table = $schema->getTable('dpatracker_subproc');

		if (!$table->hasColumn('dpa_file_name')) {
			$table->addColumn('dpa_file_name', 'string', ['notnull' => false, 'length' => 255]);
		}

		return $schema;
	}
}
