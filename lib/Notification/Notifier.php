<?php

declare(strict_types=1);

namespace OCA\DpaTracker\Notification;

use OCA\DpaTracker\AppInfo\Application;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;
use OCP\Notification\UnknownNotificationException;

class Notifier implements INotifier {
	/** @psalm-suppress PossiblyUnusedMethod */
	public function __construct(
		private IFactory $l10nFactory,
		private IURLGenerator $urlGenerator,
	) {
	}

	public function getID(): string {
		return Application::APP_ID;
	}

	public function getName(): string {
		return 'DPA Tracker';
	}

	public function prepare(INotification $notification, string $languageCode): INotification {
		if ($notification->getApp() !== Application::APP_ID) {
			throw new UnknownNotificationException();
		}

		$l = $this->l10nFactory->get(Application::APP_ID, $languageCode);

		if ($notification->getSubject() === 'review_due') {
			$params = $notification->getSubjectParameters();
			$count = (int)($params['count'] ?? 1);
			$names = (string)($params['names'] ?? '');

			$subject = $l->n(
				'%n subprocessor: review date due',
				'%n subprocessors: review dates due',
				$count,
			);

			$notification
				->setParsedSubject($subject)
				->setParsedMessage($names)
				->setLink($this->urlGenerator->linkToRouteAbsolute('dpatracker.page.index'))
				->setIcon($this->urlGenerator->getAbsoluteURL(
					$this->urlGenerator->imagePath(Application::APP_ID, 'app.svg')
				));

			return $notification;
		}

		throw new UnknownNotificationException();
	}
}
