<?php

declare(strict_types=1);

namespace OCA\DpaTracker\Controller;

use OCA\DpaTracker\AppInfo\Application;
use OCA\DpaTracker\Service\SubprocessorService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\Attribute\OpenAPI;
use OCP\AppFramework\Http\TemplateResponse;
use OC\Security\CSP\ContentSecurityPolicyNonceManager;
use OCP\IRequest;
use OCP\IUserSession;

class ExportController extends Controller {
	public function __construct(
		IRequest $request,
		private SubprocessorService $service,
		private IUserSession $userSession,
		private ContentSecurityPolicyNonceManager $nonceManager,
	) {
		parent::__construct(Application::APP_ID, $request);
	}

	#[NoAdminRequired]
	#[NoCSRFRequired]
	#[OpenAPI(OpenAPI::SCOPE_IGNORE)]
	#[FrontpageRoute(verb: 'GET', url: '/export')]
	public function pdf(): TemplateResponse {
		$userId = $this->userSession->getUser()?->getUID() ?? '';
		$subprocessors = $this->service->findAll($userId);

		return new TemplateResponse(
			Application::APP_ID,
			'export',
			[
				'subprocessors' => $subprocessors,
				'exportDate'    => date('d.m.Y'),
				'userId'        => $userId,
				'nonce'         => $this->nonceManager->getNonce(),
			],
			TemplateResponse::RENDER_AS_BLANK,
		);
	}
}
