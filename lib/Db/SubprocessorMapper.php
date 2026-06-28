<?php

declare(strict_types=1);

namespace OCA\DpaTracker\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * @template-extends QBMapper<Subprocessor>
 */
class SubprocessorMapper extends QBMapper {
	/** @psalm-suppress PossiblyUnusedMethod */
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'dpatracker_subproc', Subprocessor::class);
	}

	/**
	 * @return Subprocessor[]
	 */
	public function findAll(string $userId): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
			->orderBy('name', 'ASC');
		return $this->findEntities($qb);
	}

	/**
	 * Returns all subprocessors with a review_date on or before $today (across all users).
	 *
	 * @return Subprocessor[]
	 */
	public function findAllDue(string $today): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->isNotNull('review_date'))
			->andWhere($qb->expr()->lte('review_date', $qb->createNamedParameter($today)))
			->orderBy('user_id', 'ASC')
			->addOrderBy('review_date', 'ASC');
		return $this->findEntities($qb);
	}

	/**
	 * @throws DoesNotExistException
	 */
	public function find(int $id, string $userId): Subprocessor {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
		return $this->findEntity($qb);
	}
}
