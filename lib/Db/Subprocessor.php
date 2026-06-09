<?php

declare(strict_types=1);

namespace OCA\DpaTracker\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getName()
 * @method void setName(string $name)
 * @method ?string getPurpose()
 * @method void setPurpose(?string $purpose)
 * @method ?string getDataCategories()
 * @method void setDataCategories(?string $dataCategories)
 * @method ?string getLocation()
 * @method void setLocation(?string $location)
 * @method bool getUsParent()
 * @method void setUsParent(bool $usParent)
 * @method ?int getDpaFileId()
 * @method void setDpaFileId(?int $dpaFileId)
 * @method ?string getDpaFileName()
 * @method void setDpaFileName(?string $dpaFileName)
 * @method ?string getReviewDate()
 * @method void setReviewDate(?string $reviewDate)
 * @method ?string getCreatedAt()
 * @method void setCreatedAt(?string $createdAt)
 */
class Subprocessor extends Entity implements JsonSerializable {
	protected string $userId = '';
	protected string $name = '';
	protected ?string $purpose = null;
	protected ?string $dataCategories = null;
	protected ?string $location = null;
	protected bool $usParent = false;
	protected ?int $dpaFileId = null;
	protected ?string $dpaFileName = null;
	protected ?string $reviewDate = null;
	protected ?string $createdAt = null;

	public function jsonSerialize(): array {
		return [
			'id'             => $this->id,
			'userId'         => $this->userId,
			'name'           => $this->name,
			'purpose'        => $this->purpose,
			'dataCategories' => $this->dataCategories,
			'location'       => $this->location,
			'usParent'       => $this->usParent,
			'dpaFileId'      => $this->dpaFileId,
			'dpaFileName'    => $this->dpaFileName,
			'reviewDate'     => $this->reviewDate,
			'createdAt'      => $this->createdAt,
		];
	}
}
