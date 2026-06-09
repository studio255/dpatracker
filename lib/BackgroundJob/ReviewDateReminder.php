<?php

declare(strict_types=1);

namespace OCA\DpaTracker\BackgroundJob;

use DateTime;
use OCA\DpaTracker\AppInfo\Application;
use OCA\DpaTracker\Service\SubprocessorService;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\TimedJob;
use OCP\Notification\IManager as INotificationManager;

class ReviewDateReminder extends TimedJob {
	public function __construct(
		ITimeFactory $time,
		private SubprocessorService $service,
		private INotificationManager $notificationManager,
	) {
		parent::__construct($time);
		$this->setInterval(86400); // once per day
	}

	protected function run(mixed $argument): void {
		$today = (new DateTime())->format('Y-m-d');
		$due = $this->service->findAllDue($today);

		if (empty($due)) {
			return;
		}

		// Group by user
		$byUser = [];
		foreach ($due as $sp) {
			$byUser[$sp->getUserId()][] = $sp;
		}

		foreach ($byUser as $userId => $subprocessors) {
			$names = implode(', ', array_map(fn ($s) => $s->getName(), $subprocessors));
			$count = count($subprocessors);

			// Replace any existing unread reminder for this user
			$old = $this->notificationManager->createNotification();
			$old->setApp(Application::APP_ID)
				->setUser($userId)
				->setObject('review_due', 'summary');
			$this->notificationManager->markProcessed($old);

			$notification = $this->notificationManager->createNotification();
			$notification->setApp(Application::APP_ID)
				->setUser($userId)
				->setDateTime(new DateTime())
				->setObject('review_due', 'summary')
				->setSubject('review_due', ['count' => $count, 'names' => $names]);

			$this->notificationManager->notify($notification);
		}
	}
}
