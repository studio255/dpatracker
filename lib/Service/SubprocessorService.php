<?php

declare(strict_types=1);

namespace OCA\DpaTracker\Service;

use DateTime;
use InvalidArgumentException;
use OCA\DpaTracker\Db\Subprocessor;
use OCA\DpaTracker\Db\SubprocessorMapper;

class SubprocessorService {
	/** @psalm-suppress PossiblyUnusedMethod */
	public function __construct(
		private SubprocessorMapper $mapper,
	) {
	}

	/** @return Subprocessor[] */
	public function findAll(string $userId): array {
		return $this->mapper->findAll($userId);
	}

	public function find(int $id, string $userId): Subprocessor {
		return $this->mapper->find($id, $userId);
	}

	/** @return Subprocessor[] */
	public function findAllDue(string $today): array {
		return $this->mapper->findAllDue($today);
	}

	public function create(
		string $userId,
		string $name,
		?string $purpose,
		?string $dataCategories,
		?string $location,
		bool $usParent,
		?int $dpaFileId,
		?string $dpaFileName,
		?string $reviewDate,
	): Subprocessor {
		$this->validateName($name);
		$this->validateReviewDate($reviewDate);

		$entity = new Subprocessor();
		$entity->setUserId($userId);
		$entity->setName(trim($name));
		$entity->setPurpose($purpose);
		$entity->setDataCategories($dataCategories);
		$entity->setLocation($location);
		$entity->setUsParent($usParent);
		$entity->setDpaFileId($dpaFileId);
		$entity->setDpaFileName($dpaFileName);
		$entity->setReviewDate($reviewDate);
		$entity->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'));

		return $this->mapper->insert($entity);
	}

	public function update(
		int $id,
		string $userId,
		string $name,
		?string $purpose,
		?string $dataCategories,
		?string $location,
		bool $usParent,
		?int $dpaFileId,
		?string $dpaFileName,
		?string $reviewDate,
	): Subprocessor {
		$this->validateName($name);
		$this->validateReviewDate($reviewDate);

		$entity = $this->mapper->find($id, $userId);
		$entity->setName(trim($name));
		$entity->setPurpose($purpose);
		$entity->setDataCategories($dataCategories);
		$entity->setLocation($location);
		$entity->setUsParent($usParent);
		$entity->setDpaFileId($dpaFileId);
		$entity->setDpaFileName($dpaFileName);
		$entity->setReviewDate($reviewDate);

		return $this->mapper->update($entity);
	}

	public function delete(int $id, string $userId): void {
		$entity = $this->mapper->find($id, $userId);
		$this->mapper->delete($entity);
	}

	private function validateName(string $name): void {
		if (trim($name) === '') {
			throw new InvalidArgumentException('Name must not be empty.');
		}
	}

	private function validateReviewDate(?string $reviewDate): void {
		if ($reviewDate === null) {
			return;
		}
		$dt = DateTime::createFromFormat('Y-m-d', $reviewDate);
		if ($dt === false || $dt->format('Y-m-d') !== $reviewDate) {
			throw new InvalidArgumentException('reviewDate must be in Y-m-d format.');
		}
	}
}
