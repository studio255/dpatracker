<?php

declare(strict_types=1);

namespace OCA\DpaTracker\AppInfo;

use OCA\DpaTracker\BackgroundJob\ReviewDateReminder;
use OCA\DpaTracker\Notification\Notifier;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\BackgroundJob\IJobList;

class Application extends App implements IBootstrap {
	public const APP_ID = 'dpatracker';

	/** @psalm-suppress PossiblyUnusedMethod */
	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerNotifierService(Notifier::class);
	}

	public function boot(IBootContext $context): void {
		$context->injectFn(function (IJobList $jobList): void {
			$jobList->add(ReviewDateReminder::class);
		});
	}
}
