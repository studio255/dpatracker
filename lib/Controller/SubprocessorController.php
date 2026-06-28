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

/**
 * @psalm-suppress UnusedClass
 */
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

	/**
	 * List all subprocessors for the current user
	 *
	 * @return DataResponse<Http::STATUS_OK, list<array<string, mixed>>, array{}>
	 *
	 * 200: Subprocessors returned
	 */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'GET', url: '/subprocessors')]
	public function index(): DataResponse {
		$entities = $this->service->findAll($this->uid());
		return new DataResponse(array_values(array_map(fn ($e) => $e->jsonSerialize(), $entities)));
	}

	/**
	 * Get a subprocessor by ID
	 *
	 * @param int $id Subprocessor ID
	 * @return DataResponse<Http::STATUS_OK|Http::STATUS_NOT_FOUND, array<string, mixed>, array{}>
	 *
	 * 200: Subprocessor returned
	 * 404: Subprocessor not found
	 */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'GET', url: '/subprocessors/{id}')]
	public function show(int $id): DataResponse {
		try {
			return new DataResponse($this->service->find($id, $this->uid())->jsonSerialize());
		} catch (DoesNotExistException) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
	}

	/**
	 * Create a new subprocessor
	 *
	 * @param string $name Subprocessor name
	 * @param ?string $purpose Purpose of processing
	 * @param ?string $dataCategories Categories of personal data
	 * @param ?string $location Location or legal seat of the provider
	 * @param bool $usParent Whether the provider has a US parent company (CLOUD Act risk)
	 * @param ?int $dpaFileId Nextcloud file ID of the attached DPA document
	 * @param ?string $dpaFileName File name of the attached DPA document
	 * @param ?string $reviewDate Next contract review date (Y-m-d)
	 * @return DataResponse<Http::STATUS_CREATED|Http::STATUS_UNPROCESSABLE_ENTITY, array<string, mixed>, array{}>
	 *
	 * 201: Subprocessor created
	 * 422: Validation error
	 */
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

	/**
	 * Update an existing subprocessor
	 *
	 * @param int $id Subprocessor ID
	 * @param string $name Subprocessor name
	 * @param ?string $purpose Purpose of processing
	 * @param ?string $dataCategories Categories of personal data
	 * @param ?string $location Location or legal seat of the provider
	 * @param bool $usParent Whether the provider has a US parent company (CLOUD Act risk)
	 * @param ?int $dpaFileId Nextcloud file ID of the attached DPA document
	 * @param ?string $dpaFileName File name of the attached DPA document
	 * @param ?string $reviewDate Next contract review date (Y-m-d)
	 * @return DataResponse<Http::STATUS_OK|Http::STATUS_NOT_FOUND|Http::STATUS_UNPROCESSABLE_ENTITY, array<string, mixed>, array{}>
	 *
	 * 200: Subprocessor updated
	 * 404: Subprocessor not found
	 * 422: Validation error
	 */
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

	/**
	 * Delete a subprocessor
	 *
	 * @param int $id Subprocessor ID
	 * @return DataResponse<Http::STATUS_OK|Http::STATUS_NOT_FOUND, array{}, array{}>
	 *
	 * 200: Subprocessor deleted
	 * 404: Subprocessor not found
	 */
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
