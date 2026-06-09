<?php

declare(strict_types=1);

namespace OCA\DpaTracker\Controller;

use InvalidArgumentException;
use OCA\DpaTracker\AppInfo\Application;
use OCA\DpaTracker\Service\SubprocessorService;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\ApiRoute;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\IRequest;
use OCP\IUserSession;

class SubprocessorController extends OCSController {
	public function __construct(
		IRequest $request,
		private SubprocessorService $service,
		private IUserSession $userSession,
	) {
		parent::__construct(Application::APP_ID, $request);
	}

	private function uid(): string {
		return $this->userSession->getUser()?->getUID() ?? '';
	}

	/** @return DataResponse<Http::STATUS_OK, list<array<string, mixed>>, array{}> */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'GET', url: '/subprocessors')]
	public function index(): DataResponse {
		$entities = $this->service->findAll($this->uid());
		return new DataResponse(array_map(fn ($e) => $e->jsonSerialize(), $entities));
	}

	/** @return DataResponse<Http::STATUS_OK|Http::STATUS_NOT_FOUND, array<string, mixed>, array{}> */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'GET', url: '/subprocessors/{id}')]
	public function show(int $id): DataResponse {
		try {
			return new DataResponse($this->service->find($id, $this->uid())->jsonSerialize());
		} catch (DoesNotExistException) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
	}

	/** @return DataResponse<Http::STATUS_CREATED|Http::STATUS_UNPROCESSABLE_ENTITY, array<string, mixed>, array{}> */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'POST', url: '/subprocessors')]
	public function create(
		string $name,
		?string $purpose = null,
		?string $dataCategories = null,
		?string $location = null,
		bool $usParent = false,
		?int $dpaFileId = null,
		?string $dpaFileName = null,
		?string $reviewDate = null,
	): DataResponse {
		try {
			$entity = $this->service->create(
				$this->uid(), $name, $purpose, $dataCategories,
				$location, $usParent, $dpaFileId, $dpaFileName, $reviewDate,
			);
			return new DataResponse($entity->jsonSerialize(), Http::STATUS_CREATED);
		} catch (InvalidArgumentException $e) {
			return new DataResponse(['error' => $e->getMessage()], Http::STATUS_UNPROCESSABLE_ENTITY);
		}
	}

	/** @return DataResponse<Http::STATUS_OK|Http::STATUS_NOT_FOUND|Http::STATUS_UNPROCESSABLE_ENTITY, array<string, mixed>, array{}> */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'PUT', url: '/subprocessors/{id}')]
	public function update(
		int $id,
		string $name,
		?string $purpose = null,
		?string $dataCategories = null,
		?string $location = null,
		bool $usParent = false,
		?int $dpaFileId = null,
		?string $dpaFileName = null,
		?string $reviewDate = null,
	): DataResponse {
		try {
			$entity = $this->service->update(
				$id, $this->uid(), $name, $purpose, $dataCategories,
				$location, $usParent, $dpaFileId, $dpaFileName, $reviewDate,
			);
			return new DataResponse($entity->jsonSerialize());
		} catch (DoesNotExistException) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		} catch (InvalidArgumentException $e) {
			return new DataResponse(['error' => $e->getMessage()], Http::STATUS_UNPROCESSABLE_ENTITY);
		}
	}

	/** @return DataResponse<Http::STATUS_OK|Http::STATUS_NOT_FOUND, array{}, array{}> */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'DELETE', url: '/subprocessors/{id}')]
	public function destroy(int $id): DataResponse {
		try {
			$this->service->delete($id, $this->uid());
			return new DataResponse([]);
		} catch (DoesNotExistException) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
	}
}
